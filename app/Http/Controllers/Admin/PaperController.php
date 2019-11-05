<?php

namespace App\Http\Controllers\Admin;

use App\Services\PaperService;
use App\Services\QuestionService;
use App\Types\QuestionType;
use Edwin404\Admin\Cms\Field\FieldAttr;
use Edwin404\Admin\Cms\Field\FieldCategory;
use Edwin404\Admin\Cms\Field\FieldDate;
use Edwin404\Admin\Cms\Field\FieldDatetime;
use Edwin404\Admin\Cms\Field\FieldFields;
use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldImages;
use Edwin404\Admin\Cms\Field\FieldRichtext;
use Edwin404\Admin\Cms\Field\FieldSelect;
use Edwin404\Admin\Cms\Field\FieldSwitch;
use Edwin404\Admin\Cms\Field\FieldTag;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Field\FieldTextarea;
use Edwin404\Admin\Cms\Handle\BasicCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Base\Support\InputHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\TagHelper;
use Edwin404\Demo\Helpers\DemoHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PaperController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'paper',
        'pageTitle' => '试卷',
        'group' => 'data',
        'canDelete' => true,
        'canView' => true,
        'canAdd' => true,
        'canEdit' => true,
        'addInNewWindow' => true,
        'editInNewWindow' => true,
        'viewInNewWindow' => true,
        'fields' => [
            'categoryId' => ['type' => FieldCategory::class, 'title' => '分类', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'model' => 'paper_category', 'modelTitle' => 'name',],
            'title' => ['type' => FieldText::class, 'title' => '名称', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'search' => true,],
            'isPublic' => ['type' => FieldSwitch::class, 'title' => '公开', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'passScore' => ['type' => FieldText::class, 'title' => '及格线', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'totalScore' => ['type' => FieldText::class, 'title' => '总分', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
        ]
    ];

    private $questionService;
    private $paperService;

    public function __construct(QuestionService $questionService,
                                PaperService $paperService)
    {
        parent::__construct();
        $this->questionService = $questionService;
        $this->paperService = $paperService;
    }

    public function dataProcessView(&$item, &$record)
    {
        $item['title'] = '<a href="/paper/view/' . $record['alias'] . '" target="_blank">' . $item['title'] . '</a>';
    }

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd()
    {
        return $this->addOrEdit(0);
    }

    public function dataEdit()
    {
        return $this->addOrEdit(Input::get('_id'));
    }

    public function dataView()
    {
        $id = Input::get('_id');
        $paper = $this->paperService->getPaper($id);
        $paperQuestions = $this->paperService->listQuestions($id);
        return view('admin.paper.view', compact('id', 'paper', 'paperQuestions'));
    }

    public function dataDelete()
    {
        $_id = Input::get('_id');

        if (!is_array($_id)) {
            $ids = explode(',', $_id);
            $_id = [];
            foreach ($ids as $id) {
                if (empty($id)) {
                    continue;
                }
                $_id[] = $id;
            }
        }

        if (empty($_id)) {
            return Response::send(-1, '_id empty');
        }

        foreach ($_id as $id) {

            if (ModelHelper::exists('paper_exam', ['paperId' => $id])) {
                return Response::send(-1, '请在“考试管理”中删除所有与该试卷有关的考试');
            }

            ModelHelper::delete('paper_question', ['paperId' => $id]);
            ModelHelper::delete('paper', ['id' => $id]);
        }

        return Response::send(0, 'ok');
    }

    private function addOrEdit($id = 0)
    {
        $canEdit = true;
        if ($id) {
            $paper = ModelHelper::load('paper', ['id' => $id]);
            if (empty($paper)) {
                return Response::send(-1, 'paper not found');
            }
            if (ModelHelper::exists('paper_exam', ['paperId' => $id])) {
                $canEdit = false;
            }
        } else {
            $paper = null;
        }

        if (RequestHelper::isPost()) {

            if (DemoHelper::shouldDenyAdminDemo()) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $data = InputHelper::getJson('data');

            if (empty($data)) {
                return Response::send(-1, '提交数据为空');
            }

            $savePaper = [];
            $savePaper['title'] = trim(empty($data['title']) ? '' : $data['title']);
            $savePaper['isPublic'] = (empty($data['isPublic']) ? false : true);
            $savePaper['categoryId'] = intval(empty($data['categoryId']) ? 0 : $data['categoryId']);

            if (empty($savePaper['title'])) {
                return Response::send(-1, '试卷标题为空');
            }

            if (!$canEdit) {
                ModelHelper::updateOne('paper', ['id' => $paper['id']], $savePaper);
                return Response::send(0, null);
            }

            $savePaper['totalScore'] = intval(empty($data['totalScore']) ? '' : $data['totalScore']);
            $savePaper['passScore'] = intval(empty($data['passScore']) ? '' : $data['passScore']);
            $savePaper['timeLimitEnable'] = (empty($data['timeLimitEnable']) ? false : true);
            $savePaper['timeLimitValue'] = intval(empty($data['timeLimitValue']) ? '' : $data['timeLimitValue']);


            if (empty($savePaper['totalScore'])) {
                return Response::send(-1, '试卷总分为空');
            }
            if ($savePaper['passScore'] > $savePaper['totalScore']) {
                return Response::send(-1, '及格分数大于总分数');
            }
            if ($savePaper['timeLimitEnable']) {
                if (empty($savePaper['timeLimitValue'])) {
                    return Response::send(-1, '答题时间设置错误');
                }
            }

            // 校验数据有效性
            $totalScore = 0;
            $questionCount = 0;
            $savePaperQuestions = [];
            $savePaperQuestionMap = [];
            $saveQuestions = (empty($data['questions']) ? [] : $data['questions']);
            foreach ($saveQuestions as $index => $question) {
                $paperQuestion = [];
                if (empty($question['questionId'])) {
                    return Response::send(-1, '第' . ($index + 1) . '题不存在');
                }
                $questionData = $this->questionService->getQuestionData($question['questionId']);
                if (empty($questionData)) {
                    return Response::send(-1, '第' . ($index + 1) . '题不存在');
                }
                $paperQuestion['questionId'] = $questionData['question']['id'];
                $paperQuestion['score'] = $question['score'];
                if (isset($savePaperQuestionMap[$paperQuestion['questionId']])) {
                    return Response::send(-1, '第' . ($index + 1) . '题与第' . $savePaperQuestionMap[$paperQuestion['questionId']] . '大题重复');
                }
                $savePaperQuestionMap[$paperQuestion['questionId']] = $index + 1;
                switch ($questionData['question']['type']) {
                    case QuestionType::SINGLE_CHOICE:
                    case QuestionType::MULTI_CHOICES:
                    case QuestionType::TRUE_FALSE:
                    case QuestionType::TEXT:
                        if (!is_array($paperQuestion['score']) || count($paperQuestion['score']) != 1) {
                            return Response::send(-1, '第' . ($index + 1) . '题分值设置错误');
                        }
                        break;
                    case QuestionType::FILL:
                        if (
                            !is_array($paperQuestion['score'])
                            ||
                            count($paperQuestion['score']) != count($questionData['answers'])
                        ) {
                            return Response::send(-1, '第' . ($index + 1) . '题分值设置错误');
                        }
                        break;
                    case QuestionType::GROUP:
                        $scoreCount = 0;
                        foreach ($questionData['items'] as $item) {
                            switch ($item['question']['type']) {
                                case QuestionType::SINGLE_CHOICE:
                                case QuestionType::MULTI_CHOICES:
                                case QuestionType::TRUE_FALSE:
                                case QuestionType::TEXT:
                                    $scoreCount += 1;
                                    break;
                                case QuestionType::FILL:
                                    $scoreCount += count($item['answers']);
                                    break;
                            }
                        }
                        if (
                            !is_array($paperQuestion['score'])
                            ||
                            count($paperQuestion['score']) != $scoreCount
                        ) {
                            return Response::send(-1, '第' . ($index + 1) . '题分值设置错误');
                        }
                        break;
                }
                $questionCount += count($paperQuestion['score']);
                $totalScore += array_sum($paperQuestion['score']);
                $savePaperQuestions[] = $paperQuestion;
            }

            if ($totalScore != $savePaper['totalScore']) {
                return Response::send(-1, '总分设置' . $savePaper['totalScore'] . '和计算' . $totalScore . '不相等');
            }

            try {
                ModelHelper::transactionBegin();
                $savePaper['questionCount'] = $questionCount;
                if ($paper) {
                    $paper = ModelHelper::updateOne('paper', ['id' => $paper['id']], $savePaper);
                } else {
                    $savePaper['alias'] = strtolower(Str::random(16));
                    $paper = ModelHelper::add('paper', $savePaper);
                }
                ModelHelper::delete('paper_question', ['paperId' => $paper['id']]);
                foreach ($savePaperQuestions as $savePaperQuestion) {
                    $savePaperQuestion['paperId'] = $paper['id'];
                    $savePaperQuestion['score'] = json_encode($savePaperQuestion['score']);
                    ModelHelper::add('paper_question', $savePaperQuestion);
                }
                ModelHelper::transactionCommit();

                return Response::send(0, null);

            } catch (\Exception $e) {
                ModelHelper::transactionRollback();
                throw $e;
            }

        }

        if ($paper) {
            $paperQuestions = $this->paperService->listQuestions($paper['id']);
        } else {
            $paperQuestions = [];
        }

        $paperCategories = $this->paperService->listCategories();

        return view('admin.paper.edit', compact('id', 'paper', 'paperQuestions', 'canEdit', 'paperCategories'));
    }


    public function download(PaperService $paperService,
                             QuestionService $questionService,
                             $id)
    {
        $paper = $paperService->getPaper($id);
        $paperQuestions = $paperService->listQuestions($id);
        foreach ($paperQuestions as &$paperQuestion) {
            $paperQuestion['_questionData'] = $questionService->getQuestionData($paperQuestion['questionId']);
            $paperQuestion['_questionCount'] = count($paperQuestion['score']);
        }
        return view('admin.paper.download', [
            'paper' => $paper,
            'hasAnswer' => intval(Input::get('hasAnswer')),
            'paperQuestions' => $paperQuestions,
        ]);
    }

}