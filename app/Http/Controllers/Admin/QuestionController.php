<?php

namespace App\Http\Controllers\Admin;

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
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\TagHelper;
use Edwin404\Base\Support\TypeHelper;
use Edwin404\Demo\Helpers\DemoHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class QuestionController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'question',
        'pageTitle' => '题库',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'addInNewWindow' => true,
        'editInNewWindow' => true,
        'listFilter' => [
            'where' => [
                'parentId' => 0
            ]
        ],
        'fields' => [
            'type' => ['type' => FieldSelect::class, 'title' => '类型', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'optionType' => QuestionType::class, 'search' => true,],
            'question' => ['type' => FieldText::class, 'title' => '题目', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'search' => true,],
            'tag' => ['type' => FieldTag::class, 'title' => '标签', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'mapModel' => 'question_tag',],
            'clickCount' => ['type' => FieldText::class, 'title' => '点击量', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'testCount' => ['type' => FieldText::class, 'title' => '测试量', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'passCount' => ['type' => FieldText::class, 'title' => '通过量', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
        ]
    ];

    private $questionService;
    private $cmsAction = null;

    public function __construct(QuestionService $questionService)
    {
        parent::__construct();
        $this->questionService = $questionService;
    }

    public function dataPostDelete(&$data)
    {
        ModelHelper::delete('question_option', ['questionId' => $data['id']]);
        ModelHelper::delete('question_analysis', ['questionId' => $data['id']]);
        ModelHelper::delete('question_answer', ['questionId' => $data['id']]);
    }

    public function dataProcessView(&$item, &$record)
    {
        switch ($this->cmsAction) {
            case 'list':
                $item['question'] = HtmlHelper::text($record['question'], 100);
                $item['question'] = '<a href="/question/view/' . $record['alias'] . '" target="_blank">' . $item['question'] . '</a>';
                break;
            case 'view':
                $item['question'] = View::make('admin.question.viewItem', $this->questionService->getQuestionData($record['id']))->render();
                break;
        }
    }

    public function dataList(BasicCms $basicCms)
    {
        $this->cmsAction = 'list';
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    private function addOrEdit($id)
    {
        if (Request::isMethod('post')) {

            if (DemoHelper::shouldDenyAdminDemo()) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $question = [];
            $question['question'] = trim(Input::get('question'));
            $question['source'] = trim(Input::get('questionSource'));
            $question['type'] = InputHelper::getType('questionType', QuestionType::class);
            $question['tag'] = TagHelper::array2String(InputHelper::getArray('questionTags'));
            $question['itemCount'] = 0;

            $questionAnalysis = [];
            $questionAnalysis ['analysis'] = trim(Input::get('questionAnalysis'));

            $questionOptions = [];

            $questionAnswers = [];

            $questionItems = [];

            if (empty($question['question'])) {
                return Response::send(-1, '题目不能为空');
            }
            if (empty($question['tag'])) {
                return Response::send(-1, '题目标签为空');
            }

            switch ($question['type']) {
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::MULTI_CHOICES:
                case QuestionType::TRUE_FALSE:
                    $optionMap = [
                        QuestionType::SINGLE_CHOICE => 'singleChoiceOption',
                        QuestionType::MULTI_CHOICES => 'multiChoicesOption',
                        QuestionType::TRUE_FALSE => 'trueFalseOption',
                    ];
                    $options = InputHelper::getArray($optionMap[$question['type']]);
                    $questionOptions = [];
                    foreach ($options as $option) {
                        $op = trim($option['option']);
                        if (empty($op)) {
                            continue;
                        }
                        $questionOption = [];
                        $questionOption['isAnswer'] = (($option['isAnswer'] == 'true') ? 1 : 0);
                        $questionOption['option'] = $op;
                        $questionOptions[] = $questionOption;
                    }
                    if (empty($questionOptions)) {
                        return Response::send(-1, '题目选项为空');
                    }
                    $answerExists = false;
                    foreach ($questionOptions as $questionOption) {
                        if ($questionOption['isAnswer']) {
                            $answerExists = true;
                            break;
                        }
                    }
                    if (!$answerExists) {
                        return Response::send(-1, '题目未设置答案');
                    }
                    $question['itemCount'] = 1;
                    break;
                case QuestionType::FILL:
                    foreach (InputHelper::getArray('fillAnswer', []) as $answer) {
                        $questionAnswers[] = [
                            'answer' => $answer['answer'],
                        ];
                    }
                    if (empty($questionAnswers)) {
                        return Response::send(-1, '题目答案为空');
                    }
                    $question['itemCount'] = count($questionAnswers);
                    break;
                case QuestionType::TEXT:
                    $answers = InputHelper::getArray('textAnswer', []);
                    if (empty($answers[0]['answer'])) {
                        return Response::send(-1, '题目答案为空');
                    }
                    $questionAnswers[] = [
                        'answer' => $answers[0]['answer'],
                    ];
                    $question['itemCount'] = 1;
                    break;
                case QuestionType::GROUP:
                    $itemCount = 0;
                    foreach (InputHelper::getArray('items', []) as $index => $questionItem) {
                        $item = [];
                        $item['question'] = $questionItem['question'];
                        $item['type'] = $questionItem['type'];
                        switch ($item['type']) {
                            case QuestionType::SINGLE_CHOICE:
                                $item['singleChoiceOption'] = $questionItem['singleChoiceOption'];
                                $itemCount++;
                                break;
                            case QuestionType::MULTI_CHOICES:
                                $item['multiChoicesOption'] = $questionItem['multiChoicesOption'];
                                $itemCount++;
                                break;
                            case QuestionType::TRUE_FALSE:
                                $item['trueFalseOption'] = $questionItem['trueFalseOption'];
                                $itemCount++;
                                break;
                            case QuestionType::FILL:
                                $item['fillAnswer'] = $questionItem['fillAnswer'];
                                $itemCount += count($item['fillAnswer']);
                                break;
                            case QuestionType::TEXT:
                                $item['textAnswer'] = $questionItem['textAnswer'];
                                $itemCount++;
                                break;
                        }
                        $questionItems [] = $item;
                    }
                    if (empty($questionItems)) {
                        return Response::send(-1, '题目为空');
                    }
                    $question['itemCount'] = $itemCount;
                    break;
                default:
                    return Response::send(-1, '题目类型未能识别');
            }


            ModelHelper::transactionBegin();
            if ($id) {
                $question = ModelHelper::updateOne('question', ['id' => $id], $question);
            } else {
                $question['parentId'] = 0;
                $question['clickCount'] = 0;
                $question['testCount'] = 0;
                $question['passCount'] = 0;
                $question['alias'] = strtolower(Str::random(16));
                $question = ModelHelper::add('question', $question);
            }

            if ($id) {
                ModelHelper::updateOne('question_analysis', ['questionId' => $question['id']], $questionAnalysis);
            } else {
                $questionAnalysis['questionId'] = $question['id'];
                ModelHelper::add('question_analysis', $questionAnalysis);
            }

            if ($id) {
                ModelHelper::delete('question_option', ['questionId' => $question['id']]);
            }
            foreach ($questionOptions as $questionOption) {
                $questionOption['questionId'] = $question['id'];
                ModelHelper::add('question_option', $questionOption);
            }

            if ($id) {
                ModelHelper::delete('question_answer', ['questionId' => $question['id']]);
            }
            foreach ($questionAnswers as $questionAnswer) {
                $questionAnswer['questionId'] = $question['id'];
                ModelHelper::add('question_answer', $questionAnswer);
            }

            if ($id) {
                $ids = ModelHelper::fieldValues('question', 'id', ['parentId' => $id]);
                if (!empty($ids)) {
                    foreach ($ids as $idsId) {
                        ModelHelper::delete('question', ['id' => $idsId]);
                        ModelHelper::delete('question_option', ['questionId' => $idsId]);
                        ModelHelper::delete('question_answer', ['questionId' => $idsId]);
                        ModelHelper::delete('question_analysis', ['questionId' => $idsId]);
                    }
                }
            }
            foreach ($questionItems as $questionItem) {
                $questionItemAdd = ModelHelper::add('question', [
                    'alias' => strtolower(Str::random(16)),
                    'parentId' => $question['id'],
                    'question' => $questionItem['question'],
                    'type' => $questionItem['type'],
                ]);
                switch ($questionItem['type']) {
                    case QuestionType::SINGLE_CHOICE:
                        foreach ($questionItem['singleChoiceOption'] as $option) {
                            ModelHelper::add('question_option', [
                                'questionId' => $questionItemAdd['id'],
                                'option' => $option['option'],
                                'isAnswer' => ($option['isAnswer'] == 'true' ? 1 : 0),
                            ]);
                        }
                        break;
                    case QuestionType::MULTI_CHOICES:
                        foreach ($questionItem['multiChoicesOption'] as $option) {
                            ModelHelper::add('question_option', [
                                'questionId' => $questionItemAdd['id'],
                                'option' => $option['option'],
                                'isAnswer' => ($option['isAnswer'] == 'true' ? 1 : 0),
                            ]);
                        }
                        break;
                    case QuestionType::TRUE_FALSE:
                        foreach ($questionItem['trueFalseOption'] as $option) {
                            ModelHelper::add('question_option', [
                                'questionId' => $questionItemAdd['id'],
                                'option' => $option['option'],
                                'isAnswer' => ($option['isAnswer'] == 'true' ? 1 : 0),
                            ]);
                        }
                        break;
                    case QuestionType::FILL:
                        foreach ($questionItem['fillAnswer'] as $answer) {
                            ModelHelper::add('question_answer', [
                                'questionId' => $questionItemAdd['id'],
                                'answer' => $answer['answer'],
                            ]);
                        }
                        break;
                    case QuestionType::TEXT:
                        foreach ($questionItem['textAnswer'] as $answer) {
                            ModelHelper::add('question_answer', [
                                'questionId' => $questionItemAdd['id'],
                                'answer' => $answer['answer'],
                            ]);
                        }
                        break;
                }
            }

            ModelHelper::transactionCommit();

            if (Input::get('next') == 'true') {
                return Response::send(0, null, null, action('\App\Http\Controllers\Admin\QuestionController@dataAdd'));
            } else {
                return Response::send(0, null, null, action('\App\Http\Controllers\Admin\QuestionController@dataList'));
            }

        }

        $data = [
            'questionType' => QuestionType::SINGLE_CHOICE,
            'questionSource' => '',
            'question' => '',
            'questionTags' => [],
            'questionAnalysis' => '',
            'singleChoiceOption' => [
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
            ],
            'multiChoicesOption' => [
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
                ['isAnswer' => false, 'option' => ''],
            ],
            'trueFalseOption' => [
                ['isAnswer' => false, 'option' => '正确'],
                ['isAnswer' => false, 'option' => '错误'],
            ],
            'fillAnswer' => [
                ['answer' => ''],
            ],
            'textAnswer' => [
                ['answer' => ''],
            ],
            "items" => [
//                [
//                    'question' => '',
//                    'type' => QuestionType::SINGLE_CHOICE,
//                    'singleChoiceOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'multiChoicesOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'trueFalseOption' => [
//                        ['isAnswer' => false, 'option' => '正确'],
//                        ['isAnswer' => false, 'option' => '错误'],
//                    ],
//                    'fillAnswer' => [
//                        ['answer' => ''],
//                    ],
//                    'textAnswer' => [
//                        ['answer' => ''],
//                    ],
//                ],
//                [
//                    'question' => '',
//                    'type' => QuestionType::MULTI_CHOICES,
//                    'singleChoiceOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'multiChoicesOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'trueFalseOption' => [
//                        ['isAnswer' => false, 'option' => '正确'],
//                        ['isAnswer' => false, 'option' => '错误'],
//                    ],
//                    'fillAnswer' => [
//                        ['answer' => ''],
//                    ],
//                    'textAnswer' => [
//                        ['answer' => ''],
//                    ],
//                ],
//                [
//                    'question' => '',
//                    'type' => QuestionType::TRUE_FALSE,
//                    'singleChoiceOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'multiChoicesOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'trueFalseOption' => [
//                        ['isAnswer' => false, 'option' => '正确'],
//                        ['isAnswer' => false, 'option' => '错误'],
//                    ],
//                    'fillAnswer' => [
//                        ['answer' => ''],
//                    ],
//                    'textAnswer' => [
//                        ['answer' => ''],
//                    ],
//                ],
//                [
//                    'question' => '',
//                    'type' => QuestionType::FILL,
//                    'singleChoiceOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'multiChoicesOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'trueFalseOption' => [
//                        ['isAnswer' => false, 'option' => '正确'],
//                        ['isAnswer' => false, 'option' => '错误'],
//                    ],
//                    'fillAnswer' => [
//                        ['answer' => ''],
//                    ],
//                    'textAnswer' => [
//                        ['answer' => ''],
//                    ],
//                ],
//                [
//                    'question' => '',
//                    'type' => QuestionType::TEXT,
//                    'singleChoiceOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'multiChoicesOption' => [
//                        ['isAnswer' => false, 'option' => ''],
//                    ],
//                    'trueFalseOption' => [
//                        ['isAnswer' => false, 'option' => '正确'],
//                        ['isAnswer' => false, 'option' => '错误'],
//                    ],
//                    'fillAnswer' => [
//                        ['answer' => ''],
//                    ],
//                    'textAnswer' => [
//                        ['answer' => ''],
//                    ],
//                ],
            ],
        ];

        if ($id) {
            $question = ModelHelper::load('question', ['id' => $id]);
            $data['questionType'] = $question['type'];
            $data['questionSource'] = $question['source'];
            $data['question'] = $question['question'];
            $data['questionTags'] = TagHelper::string2Array($question['tag']);
            foreach ($data['questionTags'] as &$questionTag) {
                $questionTag = intval($questionTag);
            }
            $questionAnalysis = ModelHelper::load('question_analysis', ['questionId' => $question['id']]);
            $data['questionAnalysis'] = $questionAnalysis['analysis'];
            switch ($question['type']) {
                case QuestionType::SINGLE_CHOICE:
                    $options = [];
                    foreach (ModelHelper::find('question_option', ['questionId' => $question['id']], ['id', 'asc']) as $item) {
                        $options[] = [
                            'isAnswer' => $item['isAnswer'] ? true : false,
                            'option' => $item['option'],
                        ];
                    }
                    $data['singleChoiceOption'] = $options;
                    break;
                case QuestionType::MULTI_CHOICES:
                    $options = [];
                    foreach (ModelHelper::find('question_option', ['questionId' => $question['id']], ['id', 'asc']) as $item) {
                        $options[] = [
                            'isAnswer' => $item['isAnswer'] ? true : false,
                            'option' => $item['option'],
                        ];
                    }
                    $data['multiChoicesOption'] = $options;
                    break;
                case QuestionType::TRUE_FALSE:
                    $options = [];
                    foreach (ModelHelper::find('question_option', ['questionId' => $question['id']], ['id', 'asc']) as $item) {
                        $options[] = [
                            'isAnswer' => $item['isAnswer'] ? true : false,
                            'option' => $item['option'],
                        ];
                    }
                    $data['trueFalseOption'] = $options;
                    break;
                case QuestionType::FILL:
                    $data["fillAnswer"] = ModelHelper::model('question_answer')->where(['questionId' => $question['id']])->orderBy('id', 'asc')->get()->toArray();
                    break;
                case QuestionType::TEXT:
                    $questionAnswer = ModelHelper::load('question_answer', ['questionId' => $question['id']]);
                    $data['textAnswer'] = [
                        ['answer' => $questionAnswer['answer']]
                    ];
                    break;
                case QuestionType::GROUP:
                    $data['items'] = [];
                    $questionItems = ModelHelper::model('question')->where(['parentId' => $question['id']])->orderBy('id', 'asc')->get()->toArray();
                    foreach ($questionItems as $questionItem) {
                        $item = [];
                        $item['question'] = $questionItem['question'];
                        $item['type'] = $questionItem['type'];
                        switch ($item['type']) {
                            case QuestionType::SINGLE_CHOICE:
                                $item['singleChoiceOption'] = ModelHelper::find('question_option', ['questionId' => $questionItem['id']], ['id', 'asc']);
                                foreach ($item['singleChoiceOption'] as &$option) {
                                    $option['isAnswer'] = $option['isAnswer'] ? true : false;
                                }
                                break;
                            case QuestionType::MULTI_CHOICES:
                                $item['multiChoicesOption'] = ModelHelper::find('question_option', ['questionId' => $questionItem['id']], ['id', 'asc']);
                                foreach ($item['multiChoicesOption'] as &$option) {
                                    $option['isAnswer'] = $option['isAnswer'] ? true : false;
                                }
                                break;
                            case QuestionType::TRUE_FALSE:
                                $item['trueFalseOption'] = ModelHelper::find('question_option', ['questionId' => $questionItem['id']], ['id', 'asc']);
                                foreach ($item['trueFalseOption'] as &$option) {
                                    $option['isAnswer'] = $option['isAnswer'] ? true : false;
                                }
                                break;
                            case QuestionType::FILL:
                                $item["fillAnswer"] = ModelHelper::model('question_answer')->where(['questionId' => $questionItem['id']])->orderBy('id', 'asc')->get()->toArray();
                                break;
                            case QuestionType::TEXT:
                                $questionAnswer = ModelHelper::load('question_answer', ['questionId' => $questionItem['id']]);
                                $item['textAnswer'] = [
                                    ['answer' => $questionAnswer['answer']]
                                ];
                                break;
                        }
                        $data["items"][] = $item;
                    }

                    break;
            }

        }
        $groupTags = $this->questionService->getTags();
        return view('admin.question.edit', compact('id', 'groupTags', 'data'));
    }

    public function dataAdd()
    {
        return $this->addOrEdit(0);
    }

    public function dataEdit()
    {
        return $this->addOrEdit(Input::get('_id'));
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigData);
    }

    public function dataView(BasicCms $basicCms)
    {
        $this->cmsAction = 'view';
        return $basicCms->executeView($this, $this->cmsConfigData);
    }

    public function select()
    {
        if (Request::isMethod('post')) {

            $page = intval(Input::get('page'));
            $pageSize = 20;
            $option = [];
            $option['order'] = ['id', 'desc'];
            $option['where'] = [];
            $option['whereOperate'] = [];

            $option['where']  ['parentId'] = 0;

            if ($type = Input::get('type')) {
                $option['where']  ['type'] = $type;
            }
            if ($question = Input::get('question')) {
                $option['whereOperate'] [] = ['question', 'like', '%' . $question . '%'];
            }
            $tags = InputHelper::getArray('tags', []);
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    $option['whereOperate'] [] = ['tag', 'like', '%:' . $tag . ':%'];
                }
            }

            $list = [];
            $paginateData = ModelHelper::modelPaginate('question', $page, $pageSize, $option);
            $records = $paginateData['records'];
            foreach ($records as &$record) {
                $item = [];
                $item['id'] = $record['id'];
                $question = HtmlHelper::extractTextAndImages($record['question']);
                $item['question'] = '[' . TypeHelper::name(QuestionType::class, $record['type']) . '] ' . $question['text'];
                $item['url'] = '/question/view/' . $record['alias'];
                $list[] = $item;
            }

            $data = [];
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::generate(0, null, $data);
        }
        $groupTags = $this->questionService->getTags();
        return view('admin.question.select', compact('groupTags'));
    }

    public function preview()
    {
        $ids = InputHelper::getArray('ids', []);
        if (empty($ids)) {
            return Response::send(0, null, ['list' => []]);
        }

        $list = [];
        foreach ($ids as $id) {
            $item = [];
            $questionData = $this->questionService->getQuestionData($id);
            $item['id'] = $questionData['question']['id'];
            $item['html'] = View::make('admin.question.viewItem', $questionData)->render();
            $item['itemCount'] = $questionData['question']['itemCount'];
            $item['questionData'] = $questionData;
            $score = [];
            for ($i = 0; $i < $item['itemCount']; $i++) {
                $score[] = 5;
            }
            $item['score'] = $score;
            $list[] = $item;
        }

        return Response::send(0, null, ['list' => $list]);
    }

}