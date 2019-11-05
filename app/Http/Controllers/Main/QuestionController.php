<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Types\MemberFavoriteCategory;
use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\TagHelper;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Member\Services\MemberFavoriteService;
use Edwin404\Tecmz\Service\AdService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

class QuestionController extends BaseController
{
    public function index()
    {
        return Response::send(0, null, null, '/question/list');
    }

    public function lists($tags = '')
    {
        $urlParam = $tags;

        $questionType = null;
        $keyword = null;
        $filterTags = [];
        foreach (explode('_', $tags) as $tagId) {
            if (Str::startsWith($tagId, 't')) {
                $questionType = intval(substr($tagId, 1));
                continue;
            }
            if (Str::startsWith($tagId, 'k')) {
                $keyword = trim(substr($tagId, 1));
                continue;
            }
            $tagId = intval($tagId);
            if (empty($tagId)) {
                continue;
            }
            $filterTags[] = $tagId;
        }
        $tags = $filterTags;
        if (empty($tags)) {
            $selectedTags = [];
            $selectedTagsIds = [];
        } else {
            $selectedTags = $this->questionService->listTagsByIds($tags);
            $selectedTagsIds = [];
            foreach ($selectedTags as $selectedTag) {
                $selectedTagsIds[] = $selectedTag['id'];
            }
        }

        $pageSize = 10;
        $page = intval(Input::get('page', 1));
        if ($page < 1) {
            $page = 1;
        }

        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];
        $option['whereOperate'] = [];

        $option['where']['parentId'] = 0;

        if ($questionType) {
            $option['where']['type'] = $questionType;
        }

        foreach ($selectedTagsIds as $selectedTagsId) {
            $option['whereOperate'][] = ['tag', 'like', '%:' . $selectedTagsId . ':%'];
        }

        if ($keyword) {
            $option['whereOperate'][] = ['question', 'like', '%' . $keyword . '%'];
        }

        $paginateData = $this->questionService->paginateQuestion($page, $pageSize, $option);
        $questions = $paginateData['records'];
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));

        // SEO至关重要
        $pageTitle = [];
        if ($keyword) {
            $pageTitle[] = '搜索"' . htmlspecialchars($keyword) . '"';
        }
        if (!empty($selectedTags)) {
            foreach ($selectedTags as $tag) {
                $pageTitle[] = $tag['title'] . '题库';
            }
        }
        $pageTitle[] = ConfigFacade::get('siteName');
        $pageKeywords = join(' ', $pageTitle);
        $pageTitle[] = '第' . $page . '页';
        $pageTitle = join(' ', $pageTitle);
        $pageDescription = $pageTitle;

        $tags = $this->questionService->getTags();
        return $this->_view('question.list', compact(
            'tags', 'selectedTags', 'selectedTagsIds', 'questionType', 'questions', 'pageHtml', 'keyword',
            'pageTitle', 'pageKeywords', 'pageDescription', 'urlParam'
        ));
    }

    public function commentPost($alias)
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, '没有登录');
        }

        $content = trim(Input::get('content'));
        $content = HtmlHelper::filter($content);
        if (empty($content)) {
            return Response::send(-1, '评论内容不能为空');
        }

        $question = $this->questionService->getQuestion($alias);
        if (empty($question)) {
            return Response::send(-1, 'question empty');
        }

        $this->questionService->commentPost($this->memberUserId(), $question['id'], $content);
        $this->questionService->updateQuestionCommentCount($question['id']);

        return Response::send(0, '评论成功', null, '[reload]');
    }

    public function view(AdService $adService,
                         MemberFavoriteService $memberFavoriteService,
                         $alias)
    {
        $questionData = $this->questionService->getQuestionData(null, $alias);
        if (empty($questionData)) {
            return Response::send(-1, '题目为空');
        }

        $questionData['question']['tag'] = TagHelper::string2Array($questionData['question']['tag']);
        $questionData['question']['tag'] = TagHelper::mapInfo($questionData['question']['tag'], $this->questionService->getTagMap());

        $this->questionService->questionClick($questionData['question']['id']);

        // 查询评论
        $comments = $this->questionService->listCommentByQuestionId($questionData['question']['id']);
        ModelHelper::modelJoin($comments, 'memberUserId', '_memberUser', 'member_user', 'id');

        // 计算上一题和下一题
        $previousQuestion = null;
        $nextQuestion = null;

        $param = trim(Input::get('param'));
        $questionType = null;
        $keyword = null;
        $filterTags = [];
        foreach (explode('_', $param) as $tagId) {
            if (Str::startsWith($tagId, 't')) {
                $questionType = intval(substr($tagId, 1));
                continue;
            }
            if (Str::startsWith($tagId, 'k')) {
                $keyword = trim(substr($tagId, 1));
                continue;
            }
            $tagId = intval($tagId);
            if (empty($tagId)) {
                continue;
            }
            $filterTags[] = $tagId;
        }
        $tags = $filterTags;
        if (empty($tags)) {
            $selectedTags = [];
            $selectedTagsIds = [];
        } else {
            $selectedTags = $this->questionService->listTagsByIds($tags);
            $selectedTagsIds = [];
            foreach ($selectedTags as $selectedTag) {
                $selectedTagsIds[] = $selectedTag['id'];
            }
        }

        $pageSize = 1;
        $page = Input::get('page', 1);

        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];
        $option['whereOperate'] = [];

        if ($questionType) {
            $option['where']['type'] = $questionType;
        }

        $option['whereOperate'][] = ['id', '>', $questionData['question']['id']];

        foreach ($selectedTagsIds as $selectedTagsId) {
            $option['whereOperate'][] = ['tag', 'like', '%:' . $selectedTagsId . ':%'];
        }

        if ($keyword) {
            $option['whereOperate'][] = ['question', 'like', '%' . $keyword . '%'];
        }

        $paginateData = $this->questionService->paginateQuestion($page, $pageSize, $option);
        $questions = $paginateData['records'];
        if (!empty($questions)) {
            $previousQuestion = $questions[0];
        }

        $option['whereOperate'][0] = ['id', '<', $questionData['question']['id']];

        $paginateData = $this->questionService->paginateQuestion($page, $pageSize, $option);
        $questions = $paginateData['records'];
        if (!empty($questions)) {
            $nextQuestion = $questions[0];
        }
        return $this->_view('question.view', [
                'questionData' => $questionData,
                'previousQuestion' => $previousQuestion,
                'nextQuestion' => $nextQuestion,
                'param' => $param,
                'comments' => $comments,
                'ads' => $adService->listByPositionWithCache('pcQuestionViewRight'),
                'hasFavorite' => $memberFavoriteService->exists($this->memberUserId(), MemberFavoriteCategory::QUESTION, $questionData['question']['id'])
            ]
        );
    }

    public function commentDelete($id)
    {
        $comment = ModelHelper::load('question_comment', [
            'id' => $id
        ]);
        if (empty($comment)) {
            return Response::send(-1, '评论不存在');
        }
        if ($comment['memberUserId'] != $this->memberUserId()) {
            return Response::send(-1, '没有权限');
        }
        ModelHelper::delete('question_comment', ['id' => $comment['id']]);
        $this->questionService->updateQuestionCommentCount($comment['questionId']);
        return Response::send(0, null, null, '[reload]');
    }

    public function statCorrect($alias)
    {
        $question = $this->questionService->getQuestion($alias);
        if (empty($question)) {
            return Response::send(-1, '题目不存在');
        }
        $this->questionService->increaseQuestionTestCount($question['id']);
        $this->questionService->increaseQuestionPassCount($question['id']);
        return Response::send(0, 'ok');
    }

    public function statIncorrect($alias)
    {
        $question = $this->questionService->getQuestion($alias);
        if (empty($question)) {
            return Response::send(-1, '题目不存在');
        }
        $this->questionService->increaseQuestionTestCount($question['id']);
        return Response::send(0, 'ok');
    }

}