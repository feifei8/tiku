<?php

namespace App\Jobs;

use App\Services\PaperService;
use App\Services\QuestionService;
use App\Types\PaperExamStatus;
use App\Types\QuestionType;
use Edwin404\Base\Support\BaseJob;
use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Base\Support\ModelHelper;

class PaperExamAutoJudgeJob extends BaseJob
{
    public $paperExamId = 0;

    public function handle(QuestionService $questionService,
                           PaperService $paperService)
    {

        $paperExam = $paperService->getPaperExam($this->paperExamId);
        if (empty($paperExam)) {
            return;
        }
        if ($paperExam['isAutoJudge']) {
            return;
        }
        if ($paperExam['status'] != PaperExamStatus::SUBMITTED) {
            return;
        }

        $paperExamQuestions = $paperService->listPaperExamQuestions($paperExam['id']);

        $isAllJudged = true;

        $paperQuestions = $paperService->listQuestions($paperExam['paperId']);
        foreach ($paperQuestions as $paperQuestionIndex => $paperQuestion) {

            $paperExamQuestion = &$paperExamQuestions[$paperQuestionIndex];

            $questionData = $questionService->getQuestionData($paperQuestion['questionId']);

            switch ($questionData['question']['type']) {
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::TRUE_FALSE:
                case QuestionType::MULTI_CHOICES:
                    $answer = [];
                    foreach ($questionData['options'] as $index => $option) {
                        if ($option['isAnswer']) {
                            $answer[] = $index;
                        }
                    }
                    if (json_encode($answer) == json_encode($paperExamQuestion['answer'])) {
                        $paperExamQuestion['score'] = [$paperQuestion['score'][0]];
                    } else {
                        $paperExamQuestion['score'] = [0];
                    }
                    ModelHelper::updateOne('paper_exam_question', ['id' => $paperExamQuestion['id']], [
                        'score' => json_encode($paperExamQuestion['score']),
                        'isJudge' => true,
                    ]);
                    break;
                case QuestionType::FILL:
                    $paperExamQuestion['score'] = [];
                    $isJudge = true;
                    foreach ($questionData['answers'] as $index => $questionDataAnswer) {
                        if (trim(HtmlHelper::text($questionDataAnswer['answer'])) == trim($paperExamQuestion['answer'][$index])) {
                            $paperExamQuestion['score'][] = $paperQuestion['score'][$index];
                        } else {
                            $paperExamQuestion['score'][] = 0;
                            $isJudge = false;
                            $isAllJudged = false;
                        }
                    }
                    ModelHelper::updateOne('paper_exam_question', ['id' => $paperExamQuestion['id']], [
                        'score' => json_encode($paperExamQuestion['score']),
                        'isJudge' => $isJudge,
                    ]);
                    break;
                case QuestionType::TEXT:
                    $isJudge = false;
                    if (trim($paperExamQuestion['answer'][0]) == trim(HtmlHelper::text($questionData['answer']['answer']))) {
                        $paperExamQuestion['score'] = [$paperQuestion['score'][0]];
                        $isJudge = true;
                    } else {
                        $paperExamQuestion['score'] = [0];
                        $isAllJudged = false;
                    }
                    ModelHelper::updateOne('paper_exam_question', ['id' => $paperExamQuestion['id']], [
                        'score' => json_encode($paperExamQuestion['score']),
                        'isJudge' => $isJudge,
                    ]);
                    break;
                case QuestionType::GROUP:
                    $isJudge = true;
                    $paperExamQuestion['score'] = [];
                    $scoreIndex = 0;
                    foreach ($questionData['items'] as $questionDataItemIndex => $questionDataItem) {
                        switch ($questionDataItem['question']['type']) {
                            case QuestionType::SINGLE_CHOICE:
                            case QuestionType::TRUE_FALSE:
                            case QuestionType::MULTI_CHOICES:
                                $answer = [];
                                foreach ($questionDataItem['options'] as $index => $option) {
                                    if ($option['isAnswer']) {
                                        $answer[] = $index;
                                    }
                                }
                                if (json_encode($answer) == json_encode($paperExamQuestion['answer'][$questionDataItemIndex])) {
                                    $paperExamQuestion['score'][] = $paperQuestion['score'][$scoreIndex];
                                } else {
                                    $paperExamQuestion['score'][] = 0;
                                }
                                $scoreIndex++;
                                break;
                            case QuestionType::FILL:
                                foreach ($questionDataItem['answers'] as $index => $questionDataItemAnswer) {
                                    if (trim(HtmlHelper::text($questionDataItemAnswer['answer'])) == trim($paperExamQuestion['answer'][$questionDataItemIndex][$index])) {
                                        $paperExamQuestion['score'][] = $paperQuestion['score'][$scoreIndex];
                                    } else {
                                        $paperExamQuestion['score'][] = 0;
                                        $isJudge = false;
                                        $isAllJudged = false;
                                    }
                                    $scoreIndex++;
                                }
                                break;
                            case QuestionType::TEXT:
                                if (trim($paperExamQuestion['answer'][$questionDataItemIndex][0]) == trim(HtmlHelper::text($questionDataItem['answer']['answer']))) {
                                    $paperExamQuestion['score'][] = $paperQuestion['score'][$scoreIndex];
                                } else {
                                    $paperExamQuestion['score'][] = 0;
                                    $isJudge = false;
                                    $isAllJudged = false;
                                }
                                $scoreIndex++;
                                break;
                        }
                    }
                    ModelHelper::updateOne('paper_exam_question', ['id' => $paperExamQuestion['id']], [
                        'score' => json_encode($paperExamQuestion['score']),
                        'isJudge' => $isJudge,
                    ]);
                    break;
            }

        }

        $totalScore = null;
        if ($isAllJudged) {
            $totalScore = 0;
            foreach ($paperExamQuestions as $paperExamQuestion) {
                $totalScore += array_sum($paperExamQuestion['score']);
            }
        }

        ModelHelper::updateOne('paper_exam', ['id' => $paperExam['id']],
            [
                'isAutoJudge' => true,
                'isJudge' => $isAllJudged,
                'score' => $totalScore,
            ]
        );

    }

}