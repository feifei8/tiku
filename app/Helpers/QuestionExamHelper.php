<?php

namespace App\Helpers;


use Edwin404\Base\Support\HtmlHelper;

class QuestionExamHelper
{
    public static function isCorrect($options, $answer)
    {
        $correctAnswer = [];
        foreach ($options as $index => $option) {
            if ($option['isAnswer']) {
                $correctAnswer[] = $index;
            }
        }
        return json_encode($answer) == json_encode($correctAnswer);
    }

    public static function isTextCorrect($correctAnswer, $answer)
    {
        return trim(HtmlHelper::text($correctAnswer)) == trim($answer);
    }

    public static function optionToAnswerLabel($answer)
    {
        $labelAnswers = [];
        foreach ($answer as $item) {
            $labelAnswers[] = chr(ord('A') + $item);
        }
        return join(', ', $labelAnswers);
    }
}