<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Jobs\PaperExamAutoJudgeJob;
use App\Services\PaperService;
use App\Services\QuestionService;
use App\Types\PaperExamStatus;
use App\Types\QuestionType;
use Carbon\Carbon;
use Edwin404\Base\Support\InputHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Member\Support\MemberLoginCheck;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PaperExamController extends BaseController implements MemberLoginCheck
{
    use DispatchesJobs;

    public function index(PaperService $paperService,
                          QuestionService $questionService,
                          $alias)
    {
        $paper = $paperService->getPaperByAlias($alias);
        if (empty($paper)) {
            return Response::send(-1, '试卷不存在');
        }

        $paperQuestions = $paperService->listQuestions($paper['id']);
        foreach ($paperQuestions as &$paperQuestion) {
            $paperQuestion['_questionData'] = $questionService->getQuestionData($paperQuestion['questionId']);
        }

        return $this->_view('paper.exam', compact(
            'paper', 'paperQuestions'
        ));
    }

    public function start(PaperService $paperService,
                          QuestionService $questionService,
                          $alias)
    {
        $paper = $paperService->getPaperByAlias($alias);
        if (empty($paper)) {
            return Response::send(-1, '试卷不存在');
        }

        $paperQuestions = $paperService->listQuestions($paper['id']);
        foreach ($paperQuestions as $k => $paperQuestion) {
            $paperQuestions[$k]['_questionData'] = $questionService->getQuestionData($paperQuestion['questionId']);
        }

        //return $paperQuestions;

        $paperQuestionList = [];
        $number = 1;
        foreach ($paperQuestions as $paperQuestion) {
            $paperQuestionListItem = [];
            $paperQuestionListItem['score'] = $paperQuestion['score'];

            $paperQuestionListItem['questionNumber'] = $number;
            $questionCount = 0;

            $paperQuestionListItem['question'] = [];
            $paperQuestionListItem['question']['id'] = $paperQuestion['_questionData']['question']['id'];
            $paperQuestionListItem['question']['type'] = $paperQuestion['_questionData']['question']['type'];
            $paperQuestionListItem['question']['question'] = $paperQuestion['_questionData']['question']['question'];

            switch ($paperQuestionListItem['question']['type']) {
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::TRUE_FALSE:
                case QuestionType::MULTI_CHOICES:
                    $paperQuestionListItem['options'] = [];
                    foreach ($paperQuestion['_questionData']['options'] as $option) {
                        $paperQuestionListItem['options'][] = [
                            'isAnswer' => false,
                            'option' => $option['option'],
                        ];
                    }
                    $questionCount = 1;
                    break;
                case QuestionType::FILL:
                    $paperQuestionListItem['answers'] = [];
                    foreach ($paperQuestion['_questionData']['answers'] as $answer) {
                        $paperQuestionListItem['answers'][] = [
                            'answer' => '',
                        ];
                    }
                    $questionCount = count($paperQuestionListItem['answers']);
                    break;
                case QuestionType::TEXT:
                    $paperQuestionListItem['answer'] = [
                        'answer' => '',
                    ];
                    $questionCount = 1;
                    break;
                case QuestionType::GROUP:
                    $paperQuestionListItem['items'] = [];
                    $questionCount = 0;
                    foreach ($paperQuestion['_questionData']['items'] as $item) {
                        $questionItem = [];
                        $questionItem['question'] = [];
                        $questionItem['question']['type'] = $item['question']['type'];
                        $questionItem['question']['question'] = $item['question']['question'];
                        $questionItem['itemNumber'] = $item['itemNumber'];
                        $questionItem['itemCount'] = $item['itemCount'];
                        switch ($questionItem['question']['type']) {
                            case QuestionType::SINGLE_CHOICE:
                            case QuestionType::TRUE_FALSE:
                            case QuestionType::MULTI_CHOICES:
                                $questionItem['options'] = [];
                                foreach ($item['options'] as $option) {
                                    $questionItem['options'][] = [
                                        'isAnswer' => false,
                                        'option' => $option['option'],
                                    ];
                                }
                                $questionCount++;
                                break;
                            case QuestionType::FILL:
                                $questionItem['answers'] = [];
                                foreach ($item['answers'] as $answer) {
                                    $questionItem['answers'][] = [
                                        'answer' => '',
                                    ];
                                }
                                $questionCount += count($questionItem['answers']);
                                break;
                            case QuestionType::TEXT:
                                $questionItem['answer'] = [
                                    'answer' => '',
                                ];
                                $questionCount++;
                                break;
                        }
                        $paperQuestionListItem['items'][] = $questionItem;
                    }
                    break;
            }

            $paperQuestionListItem['questionCount'] = $questionCount;
            $number += $questionCount;

            $paperQuestionList[] = $paperQuestionListItem;
        }

        $paperExam = ModelHelper::load('paper_exam', [
            'memberUserId' => $this->memberUserId(),
            'paperId' => $paper['id'],
            'status' => PaperExamStatus::DOING,
        ]);
        if (empty($paperExam)) {
            $paperExam = ModelHelper::add('paper_exam', [
                'memberUserId' => $this->memberUserId(),
                'paperId' => $paper['id'],
                'status' => PaperExamStatus::DOING,
                'startTime' => Carbon::now(),
                'isAutoJudge' => false,
                'isJudge' => false,
            ]);
            $msg = null;
        } else {
            $msg = '即将继续上次未完成的考试';
        }

        $startTime = Carbon::parse($paperExam['startTime'])->format('Y-m-d H:i:s');
        $timeLimitEnable = ($paper['timeLimitEnable'] ? true : false);
        $timeLeftSecond = 0;
        if ($timeLimitEnable) {
            $timeLeftSecond = $paper['timeLimitValue'] * 60 - (time() - strtotime($startTime));
            $timeLeftSecond = max($timeLeftSecond, 0);
        }

        return Response::send(0, $msg, [
            'startTime' => $startTime,
            'timeLimitEnable' => $timeLimitEnable,
            'timeLeftSecond' => $timeLeftSecond,
            'paperQuestionList' => $paperQuestionList,
        ]);
    }

    public function submit($alias)
    {
        return $this->saveOrSubmit($alias, true);
    }

    public function save($alias)
    {
        return $this->saveOrSubmit($alias, false);
    }

    private function saveOrSubmit($alias, $isSubmit)
    {
        $paper = $this->paperService->getPaperByAlias($alias);
        if (empty($paper)) {
            return Response::send(-1, '试卷不存在');
        }

        $paperExam = ModelHelper::load('paper_exam', [
            'memberUserId' => $this->memberUserId(),
            'paperId' => $paper['id'],
            'status' => PaperExamStatus::DOING,
        ]);
        if (empty($paperExam)) {
            return Response::send(-1, '试卷考试不存在或者已经提交');
        }

        if ($paper['timeLimitEnable']) {
            $timeLeftSecond = $paper['timeLimitValue'] * 60 - (time() - strtotime($paperExam['startTime']));
            if ($timeLeftSecond <= 0) {
                ModelHelper::updateOne('paper_exam', ['id' => $paperExam['id']], [
                    'status' => PaperExamStatus::SUBMITTED,
                ]);
                $job = new PaperExamAutoJudgeJob();
                $job->paperExamId = $paperExam['id'];
                $this->dispatch($job);
                return Response::send(-1, '考试时间到,试卷已强制提交', null, '/member/exam');
            }
        }

        $paperQuestions = $this->paperService->listQuestions($paper['id']);
        foreach ($paperQuestions as $k => $questionData) {
            $paperQuestions[$k]['_questionData'] = $this->questionService->getQuestionData($questionData['questionId']);
        }

        $data = InputHelper::getJson('data');
        if (empty($data) || !is_array($data)) {
            return Response::send(-1, '考试数据提交错误');
        }

        $examQuestionList = [];
        foreach ($paperQuestions as $index => $paperQuestion) {
            if (empty($data[$index])) {
                return Response::send(-1, '考试数据提交错误');
            }
            $questionDataSubmitted = $data[$index];
            $questionData = $paperQuestion['_questionData'];
            $examQuestion = [];
            $examQuestion['examId'] = $paperExam['id'];
            $examQuestion['questionId'] = $questionData['question']['id'];
            $examQuestion['answer'] = [];
            switch ($questionData['question']['type']) {
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::TRUE_FALSE:
                case QuestionType::MULTI_CHOICES:
                    foreach ($questionDataSubmitted['options'] as $optionIndex => $option) {
                        if ($option['isAnswer']) {
                            $examQuestion['answer'][] = $optionIndex;
                        }
                    }
                    break;
                case QuestionType::FILL:
                    foreach ($questionDataSubmitted['answers'] as $answer) {
                        $examQuestion['answer'][] = $answer['answer'];
                    }
                    break;
                case QuestionType::TEXT:
                    $examQuestion['answer'] [] = $questionDataSubmitted['answer']['answer'];
                    break;
                case QuestionType::GROUP:
                    foreach ($questionDataSubmitted['items'] as $item) {
                        $answer = [];
                        switch ($item['question']['type']) {
                            case QuestionType::SINGLE_CHOICE:
                            case QuestionType::TRUE_FALSE:
                            case QuestionType::MULTI_CHOICES:
                                foreach ($item['options'] as $optionIndex => $option) {
                                    if ($option['isAnswer']) {
                                        $answer[] = $optionIndex;
                                    }
                                }
                                break;
                            case QuestionType::FILL:
                                foreach ($item['answers'] as $answerItem) {
                                    $answer[] = $answerItem['answer'];
                                }
                                break;
                            case QuestionType::TEXT:
                                $answer[] = $item['answer']['answer'];
                                break;
                        }
                        $examQuestion['answer'] [] = $answer;
                    }
                    break;
            }
            $examQuestionList[] = $examQuestion;
        }

        foreach ($examQuestionList as $examQuestion) {
            $exists = ModelHelper::load('paper_exam_question', [
                'examId' => $examQuestion['examId'],
                'questionId' => $examQuestion['questionId'],
            ]);
            if ($exists) {
                ModelHelper::updateOne('paper_exam_question', ['id' => $exists['id']], [
                    'answer' => @json_encode($examQuestion['answer']),
                    'isJudge' => false,
                ]);
            } else {
                ModelHelper::add('paper_exam_question', [
                    'examId' => $examQuestion['examId'],
                    'questionId' => $examQuestion['questionId'],
                    'answer' => @json_encode($examQuestion['answer']),
                    'isJudge' => false,
                ]);
            }
        }

        if (!$isSubmit) {

            $startTime = Carbon::parse($paperExam['startTime'])->format('Y-m-d H:i:s');
            $timeLimitEnable = ($paper['timeLimitEnable'] ? true : false);
            $timeLeftSecond = 0;
            if ($timeLimitEnable) {
                $timeLeftSecond = $paper['timeLimitValue'] * 60 - (time() - strtotime($startTime));
                $timeLeftSecond = max($timeLeftSecond, 0);
            }

            return Response::send(0, '保存成功', compact('timeLeftSecond'));
        }

        ModelHelper::updateOne('paper_exam', ['id' => $paperExam['id']], [
            'status' => PaperExamStatus::SUBMITTED,
        ]);
        $job = new PaperExamAutoJudgeJob();
        $job->paperExamId = $paperExam['id'];
        $this->dispatch($job);

        return Response::send(0, '提交成功', null, '/member/exam');
    }

}