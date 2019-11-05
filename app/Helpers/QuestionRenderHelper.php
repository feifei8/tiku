<?php

namespace App\Helpers;


use App\Types\QuestionType;
use Illuminate\Support\Facades\View;

class QuestionRenderHelper
{
    public static function render($questionData, $number = null, $option = [])
    {
        switch ($questionData['question']['type']) {
            case QuestionType::SINGLE_CHOICE:
                return self::renderSingleChoice($questionData, $number, $option);
            case QuestionType::MULTI_CHOICES:
                return self::renderMultiChoice($questionData, $number, $option);
            case QuestionType::TRUE_FALSE:
                return self::renderTrueFalse($questionData, $number, $option);
            case QuestionType::FILL:
                return self::renderFill($questionData, $number, $option);
            case QuestionType::TEXT:
                return self::renderText($questionData, $number, $option);
            case QuestionType::GROUP:
                $html = [];
                foreach ($questionData['items'] as $questionDataItem) {
                    $html[] = self::render($questionDataItem, $number, $option);
                    if (null !== $number) {
                        $number++;
                    }
                }
                $groupQuestionHtml = join("\n", $html);
                return self::renderGroup($questionData, $groupQuestionHtml, $number, $option);
        }
    }

    public static function renderBase()
    {
        return 'theme.default.public.question';
    }

    public static function renderGroup($questionData, $groupQuestionHtml, $number, $option)
    {
        return View::make(self::renderBase() . '.group', [
            'questionData' => $questionData,
            'groupQuestionHtml' => $groupQuestionHtml,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

    public static function renderSingleChoice($questionData, $number, $option)
    {
        return View::make(self::renderBase() . '.singleChoice', [
            'questionData' => $questionData,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

    public static function renderMultiChoice($questionData, $number, $option)
    {
        return View::make(self::renderBase() . '.multiChoice', [
            'questionData' => $questionData,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

    public static function renderTrueFalse($questionData, $number, $option)
    {
        return View::make(self::renderBase() . '.trueFalse', [
            'questionData' => $questionData,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

    public static function renderFill($questionData, $number, $option)
    {
        return View::make(self::renderBase() . '.fill', [
            'questionData' => $questionData,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

    public static function renderText($questionData, $number, $option)
    {
        return View::make(self::renderBase() . '.text', [
            'questionData' => $questionData,
            'number' => $number,
            'option' => $option,
        ])->render();
    }

}