<?php

namespace App\Services;


use Edwin404\Base\Support\ModelHelper;

class PaperService
{
    public function listQuestions($paperId)
    {
        $paperQuestions = ModelHelper::find('paper_question', ['paperId' => $paperId], ['id', 'asc']);
        ModelHelper::decodeRecordsJson($paperQuestions, 'score');
        return $paperQuestions;
    }

    public function listCategories()
    {
        return ModelHelper::find('paper_category', [], ['sort', 'asc']);
    }

    public function getPaper($id)
    {
        return ModelHelper::load('paper', ['id' => $id]);
    }

    public function getPaperByAlias($alias)
    {
        return ModelHelper::load('paper', ['alias' => $alias]);
    }

    public function getPaperExam($id)
    {
        $paperExam = ModelHelper::load('paper_exam', ['id' => $id]);
        return $paperExam;
    }

    public function listPaperExamQuestions($paperExamId)
    {
        $list = ModelHelper::find('paper_exam_question', ['examId' => $paperExamId], ['id', 'asc']);
        ModelHelper::decodeRecordsJson($list, 'answer');
        ModelHelper::decodeRecordsJson($list, 'score');
        return $list;
    }

}