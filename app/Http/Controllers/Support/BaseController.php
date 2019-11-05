<?php

namespace App\Http\Controllers\Support;

use App\Services\PaperService;
use App\Services\QuestionService;
use Edwin404\Article\Services\ArticleService;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Common\Support\TemplateViewTrait;
use Edwin404\Member\Support\MemberTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    protected $articleService;
    protected $questionService;
    protected $paperService;

    use MemberTrait;
    use TemplateViewTrait;

    public function __construct(ArticleService $articleService,
                                QuestionService $questionService,
                                PaperService $paperService)
    {
        $this->articleService = $articleService;
        $this->questionService = $questionService;
        $this->paperService = $paperService;

        $this->memberUserSetup();
        $this->basicSetup();

        if ($this->memberUserId()) {
            $memberUser = $this->memberUser();
            if (empty($memberUser['username']) && !in_array(View::shared('request_path'), [
                    '/register/bind',
                    '/logout',
                ])
            ) {
                header('Location: /register/bind?redirect=' . urlencode(RequestHelper::currentPageUrl()));
                exit();
            }
        }

    }

    protected function basicSetup()
    {
        $footerArticles = $this->articleService->listByPosition('footer');
        View::share('footerArticles', $footerArticles);
    }

}