<?php

namespace App\Services;


use App\Types\QuestionType;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\TagHelper;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    public function getQuestion($alias)
    {
        $question = ModelHelper::load('question', ['alias' => $alias]);
        return $question;
    }

    public function getQuestionData($questionId, $questionAlias = null)
    {
        $data = [];

        if (null === $questionAlias) {
            $question = ModelHelper::load('question', ['id' => $questionId]);
        } else {
            $question = ModelHelper::load('question', ['alias' => $questionAlias]);
        }
        if (empty($question)) {
            return null;
        }
        $data['question'] = $question;
        $data['analysis'] = ModelHelper::load('question_analysis', ['questionId' => $question['id']]);
        switch ($question['type']) {
            case QuestionType::SINGLE_CHOICE:
            case QuestionType::MULTI_CHOICES:
            case QuestionType::TRUE_FALSE:
                $data['options'] = ModelHelper::find('question_option', ['questionId' => $question['id']], ['id', 'asc']);
            case QuestionType::FILL:
                $data['answers'] = ModelHelper::find('question_answer', ['questionId' => $question['id']], ['id', 'asc']);
                break;
            case QuestionType::TEXT:
                $data['answer'] = ModelHelper::load('question_answer', ['questionId' => $question['id']]);
                break;
            case QuestionType::GROUP:
                $questionItems = ModelHelper::find('question', ['parentId' => $question['id']], ['id', 'asc']);
                $data['items'] = [];
                $itemNumber = 1;
                foreach ($questionItems as $questionItem) {
                    $item = [];
                    $item['itemNumber'] = $itemNumber;
                    $item['itemCount'] = 1;
                    $item['question'] = $questionItem;
                    switch ($questionItem['type']) {
                        case QuestionType::SINGLE_CHOICE:
                        case QuestionType::MULTI_CHOICES:
                        case QuestionType::TRUE_FALSE:
                            $item['options'] = ModelHelper::find('question_option', ['questionId' => $questionItem['id']], ['id', 'asc']);
                            $itemNumber++;
                            break;
                        case QuestionType::FILL:
                            $item['answers'] = ModelHelper::find('question_answer', ['questionId' => $questionItem['id']], ['id', 'asc']);
                            $item['itemCount'] = count($item['answers']);
                            $itemNumber += $item['itemCount'];
                            break;
                        case QuestionType::TEXT:
                            $item['answer'] = ModelHelper::load('question_answer', ['questionId' => $questionItem['id']]);
                            $itemNumber++;
                            break;
                    }
                    $data['items'][] = $item;
                }
                break;
        }
        return $data;
    }

    public function questionClick($questionId)
    {
        ModelHelper::updateOne('question', ['id' => $questionId], ['clickCount' => DB::raw('clickCount+1')]);
    }

    /**
     * 获取题目标签(包含分组)
     *
     * array(
     *      array(
     *          'groupId'=>1,
     *          'groupTitle'=>'分组',
     *          'groupTags'=>array(
     *              'id'=>1,
     *              'title'=>'标签1',
     *          )
     *      )
     * )
     */
    public function getTags()
    {
        $questionTags = ModelHelper::find('question_tag');
        $questionTagGroups = ModelHelper::find('question_tag_group', [], ['sort', 'asc']);
        $groupTags = [];

        foreach ($questionTagGroups as &$questionTagGroup) {
            $group = [];
            $group['groupId'] = $questionTagGroup['id'];
            $group['groupTitle'] = $questionTagGroup['title'];
            $tags = [];
            foreach ($questionTags as &$questionTag) {
                if ($questionTag['groupId'] == $questionTagGroup['id']) {
                    $tags[] = [
                        'id' => $questionTag['id'],
                        'title' => $questionTag['title'],
                    ];
                }
            }
            $group['groupTags'] = $tags;
            $groupTags[] = $group;
        }

        return $groupTags;
    }

    public function getTagMap()
    {
        $questionTags = ModelHelper::find('question_tag');
        $map = [];
        foreach ($questionTags as $questionTag) {
            $map[$questionTag['id']] = $questionTag;
        }
        return $map;
    }

    public function listTagsByIds($ids)
    {
        return ModelHelper::model('question_tag')->whereIn('id', $ids)->get()->toArray();
    }

    public function paginateQuestion($page, $pageSize, $option = [])
    {
        $option['where']['parentId'] = 0;
        $paginateData = ModelHelper::modelPaginate('question', $page, $pageSize, $option);
        $tagMap = $this->getTagMap();
        foreach ($paginateData['records'] as &$item) {
            $item['tag'] = TagHelper::string2Array($item['tag']);
            $item['tag'] = TagHelper::mapInfo($item['tag'], $tagMap);
        }
        return $paginateData;
    }

    public function listCommentByQuestionId($questionId)
    {
        $comments = ModelHelper::find('question_comment', ['questionId' => $questionId], ['id', 'asc']);
        return $comments;
    }

    public function commentPost($memberUserId, $questionId, $content)
    {
        return
            ModelHelper::add('question_comment', [
                'memberUserId' => $memberUserId,
                'questionId' => $questionId,
                'content' => $content,
            ]);
    }

    public function updateQuestionCommentCount($questionId)
    {
        $commentCount = ModelHelper::count('question_comment', [
            'questionId' => $questionId
        ]);
        ModelHelper::updateOne('question', [
            'id' => $questionId,
        ], [
            'commentCount' => $commentCount
        ]);
    }

    public function increaseQuestionTestCount($questionId)
    {
        ModelHelper::model('question')->where(['id' => $questionId])->increment('testCount');
    }

    public function increaseQuestionPassCount($questionId)
    {
        ModelHelper::model('question')->where(['id' => $questionId])->increment('passCount');
    }
}