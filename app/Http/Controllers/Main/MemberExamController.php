<?php

namespace App\Http\Controllers\Main;


use App\Helpers\MailHelper;
use App\Helpers\SmsHelper;
use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Data\Services\DataService;
use Edwin404\Forum\Events\ThreadHasNewPost;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Member\Services\MemberMessageService;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Support\MemberLoginCheck;
use Edwin404\Member\Types\MemberMessageStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MemberExamController extends BaseController implements MemberLoginCheck
{
    public function index()
    {
        $page = intval(Input::get('page', 1));
        if($page<1){
            $page = 1;
        }
        $pageSize = 10;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];

        $option['where']['memberUserId'] = $this->memberUserId();

        $paginateData = ModelHelper::modelPaginate('paper_exam', $page, $pageSize, $option);
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));
        ModelHelper::modelJoin($paginateData['records'], 'paperId', '_paper', 'paper', 'id');
        $paperExams = $paginateData['records'];

        return $this->_view('member.exam.index', compact(
            'paperExams',
            'pageHtml'
        ));
    }

    public function view($id)
    {
        $paperExam = $this->paperService->getPaperExam($id);
        if (empty($paperExam) || $paperExam['memberUserId'] != $this->memberUserId()) {
            return Response::send(-1, '考试不存在');
        }

        if (!$paperExam['isJudge']) {
            return Response::send(-1, '正在阅卷,请稍后查看');
        }

        $paperExamQuestions = $this->paperService->listPaperExamQuestions($paperExam['id']);

        $paper = $this->paperService->getPaper($paperExam['paperId']);
        if (empty($paper)) {
            return Response::send(-1, '试卷不存在');
        }

        $paperQuestions = $this->paperService->listQuestions($paper['id']);
        foreach ($paperQuestions as &$paperQuestion) {
            $paperQuestion['_questionData'] = $this->questionService->getQuestionData($paperQuestion['questionId']);
        }

        return $this->_view('member.exam.view', compact(
            'paper', 'paperQuestions', 'paperExam', 'paperExamQuestions'
        ));
    }
}