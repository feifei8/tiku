<?php
namespace App\Http\Controllers\Admin;

use App\Services\PaperService;
use App\Services\QuestionService;
use App\Types\PaperExamStatus;
use App\Types\QuestionType;
use Edwin404\Admin\Cms\Field\FieldAttr;
use Edwin404\Admin\Cms\Field\FieldCategory;
use Edwin404\Admin\Cms\Field\FieldDate;
use Edwin404\Admin\Cms\Field\FieldDatetime;
use Edwin404\Admin\Cms\Field\FieldEmpty;
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
use Edwin404\SmartAssets\Helper\AssetsHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PaperExamController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'paper_exam',
        'pageTitle' => '阅卷管理',
        'group' => 'data',
        'canDelete' => true,
        'canEdit' => true,
        'editInNewWindow' => true,
        'fields' => [
            '_paper' => ['type' => FieldEmpty::class, 'title' => '试卷', 'list' => true,],
            '_memberUser' => ['type' => FieldEmpty::class, 'title' => '用户', 'list' => true,],
            'status' => ['type' => FieldSelect::class, 'title' => '状态', 'list' => true, 'search' => true, 'optionType' => PaperExamStatus::class,],
            'isJudge' => ['type' => FieldSwitch::class, 'title' => '已经阅卷', 'list' => true, 'search' => true, 'optionType' => PaperExamStatus::class,],
            'startTime' => ['type' => FieldDatetime::class, 'title' => '开始时间', 'list' => true,],
            'score' => ['type' => FieldDatetime::class, 'title' => '分数', 'list' => true,],
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

    public function dataProcessViewField($key, &$record)
    {
        switch ($key) {
            case '_memberUser':
                $memberUser = ModelHelper::load('member_user', ['id' => $record['memberUserId']]);
                if (empty($memberUser)) {
                    return '[未知用户]';
                }
                return '<a href="javascript:;" data-dialog-request="' . action('\App\Http\Controllers\Admin\MemberController@dataView', ['_id' => $record['memberUserId']]) . '"><img src="' . AssetsHelper::fix($memberUser['avatar']) . '" style="width:30px;height:30px;" />' . htmlspecialchars($memberUser['username']) . '</a>';
            case '_paper':
                $paper = ModelHelper::load('paper', ['id' => $record['paperId']]);
                return '<a target="_blank" href="/paper/view/' . $paper['alias'] . '">' . htmlspecialchars($paper['title']) . '</a>';
        }
    }

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataEdit()
    {
        $id = Input::get('_id');
        $paperExam = ModelHelper::load('paper_exam', ['id' => $id]);
        $paperExamQuestions = $this->paperService->listPaperExamQuestions($paperExam['id']);

        if (RequestHelper::isPost()) {

            if (DemoHelper::shouldDenyAdminDemo()) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $data = InputHelper::getJson('data');

            if (empty($data)) {
                return Response::send(-1, '提交数据为空');
            }

            $totalScore = 0;
            $paperExamQuestions = $data;
            foreach ($paperExamQuestions as $paperExamQuestion) {
                ModelHelper::updateOne('paper_exam_question', ['id' => $paperExamQuestion['id']], [
                    'score' => json_encode($paperExamQuestion['score'])
                ]);
                foreach ($paperExamQuestion['score'] as $item) {
                    $totalScore += $item;
                }
            }

            ModelHelper::updateOne('paper_exam', [
                'id' => $paperExam['id']
            ], [
                'score' => $totalScore,
                'isJudge' => true,
            ]);

            return Response::send(0, '提交成功', null, action('\App\Http\Controllers\Admin\PaperExamController@dataList'));

        }

        $paper = $this->paperService->getPaper($paperExam['paperId']);
        $paperQuestions = $this->paperService->listQuestions($paperExam['paperId']);
        return view('admin.paperExam.edit', compact('id', 'paper', 'paperQuestions', 'paperExamQuestions'));
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
            ModelHelper::delete('paper_exam', ['id' => $id]);
            ModelHelper::delete('paper_exam_question', ['examId' => $id]);
        }

        return Response::send(0, 'ok');
    }


}