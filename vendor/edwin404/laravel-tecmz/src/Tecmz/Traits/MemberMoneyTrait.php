<?php

namespace Edwin404\Tecmz\Traits;


use App\Constant\PayConstant;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Member\Services\MemberMoneyService;
use Edwin404\Member\Services\MemberService;
use Edwin404\Oauth\Types\OauthType;
use Edwin404\Pay\Services\PayOrderService;
use Edwin404\Pay\Types\PayType;
use Edwin404\Tecmz\Helpers\PayHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

trait MemberMoneyTrait
{
    public function index(MemberMoneyService $memberMoneyService)
    {
        $pageSize = 10;
        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];

        $page = 1;
        $paginateData = $memberMoneyService->paginateLog($this->memberUserId(), $page, $pageSize, $option);

        return $this->_view('member.money.index', [
            'totalMoney' => $memberMoneyService->getTotal($this->memberUserId()),
            'records' => $paginateData['records'],
        ]);
    }

    public function log(MemberMoneyService $memberMoneyService)
    {
        $pageSize = 10;
        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];

        if (RequestHelper::isPost()) {
            $page = intval(Input::get('page', 1));
            $paginateData = $memberMoneyService->paginateLog($this->memberUserId(), $page, $pageSize, $option);
            $html = null;
            if (!empty($paginateData['records'])) {
                $html = $this->_viewRender('member.money.logItems', [
                    'records' => $paginateData['records'],
                ]);
            }
            return Response::send(0, null, [
                'html' => $html,
                'page' => $page,
                'pageSize' => $pageSize,
                'total' => $paginateData['total'],
            ]);
        }

        $page = 1;
        $paginateData = $memberMoneyService->paginateLog($this->memberUserId(), $page, $pageSize, $option);
        return $this->_view('member.money.log', [
            'total' => $paginateData['total'],
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $paginateData['records'],
        ]);
    }

    public function charge(PayOrderService $payOrderService,
                           MemberMoneyService $memberMoneyService,
                           MemberService $memberService)
    {
        $openId = $memberService->getOauthOpenId($this->memberUserId(), OauthType::WECHAT_MOBILE);
        if (empty($openId)) {
            return Response::send(
                -1,
                null,
                null,
                '/oauth_login_' . OauthType::WECHAT_MOBILE . '?redirect=' . urlencode(View::shared('request_path'))
            );
        }

        if (RequestHelper::isPost()) {
            $input = InputPackage::buildFromInput();
            $payType = $input->getType('payType', PayType::class, null);
            if (empty($payType)) {
                return Response::send(-1, '支付方式错误');
            }
            if (!PayHelper::isPayEnable($payType)) {
                return Response::send(-1, '支付方式未开启');
            }

            $fee = $input->getDecimal('fee');
            if ($fee < 0.01) {
                return Response::send(-1, '充值金额不能为空');
            }

            $data = [];
            $data['payType'] = $payType;

            switch ($payType) {

                case PayType::WECHAT_MOBILE:
                    $memberMoneyCharge = $memberMoneyService->createChange($this->memberUserId(), $fee);
                    $ret = $payOrderService->create(
                        PayConstant::CHARGE,
                        $memberMoneyCharge['id'],
                        $payType,
                        $memberMoneyCharge['fee'],
                        '订单:' . $memberMoneyCharge['sn'],
                        '订单:' . $memberMoneyCharge['sn'],
                        '/member/money',
                        ['openId' => $openId]
                    );
                    if ($ret['code']) {
                        return Response::send(-1, $ret['msg']);
                    }
                    $data['json'] = $ret['data']['json'];
                    $data['successRedirect'] = $ret['data']['successRedirect'];
                    return Response::send(0, null, $data);
                default:
                    return Response::send(-1, '支付方式错误');
            }
        }
        return $this->_view('member.money.charge', [
        ]);
    }

//    public function cash(MemberMoneyService $memberMoneyService)
//    {
//
//        if (RequestHelper::isPost()) {
//            $money = trim(Input::get('money'));
//            $alipayRealname = trim(Input::get('alipayRealname'));
//            $alipayAccount = trim(Input::get('alipayAccount'));
//            if (empty($money)) {
//                return Response::send(-1, '提现金额不能为空');
//            }
//            if (empty($alipayRealname)) {
//                return Response::send(-1, '支付宝姓名不能为空');
//            }
//            if (empty($alipayAccount)) {
//                return Response::send(-1, '支付宝账号不能为空');
//            }
//            if ($money < 100) {
//                return Response::send(-1, '提现金额至少为100');
//            }
//            $total = $memberMoneyService->getTotal($this->memberUserId());
//            if ($total < 100) {
//                return Response::send(-1, '当前账户余额不满100,不能提现');
//            }
//
//            $rate = 100 - intval(ConfigFacade::get('customTipCashRate', 0));
//            if ($rate < 0) {
//                $rate = 0;
//            } elseif ($rate > 100) {
//                $rate = 100;
//            }
//            $moneyAfterTax = bcdiv(bcmul($money, $rate, 2), 100, 2);
//
//            try {
//                ModelHelper::transactionBegin();
//                $memberMoneyService->cash($this->memberUserId(), $money, $moneyAfterTax, MemberMoneyCashType::ALIPAY, $alipayRealname, $alipayAccount);
//                ModelHelper::transactionCommit();
//            } catch (\Exception $e) {
//                ModelHelper::transactionRollback();
//                throw $e;
//            }
//
//            return Response::send(0, '余额提现申请提交成功', null, '/member/money');
//        }
//
//        return $this->_view('member.money.cash', [
//            'total' => $memberMoneyService->getTotal($this->memberUserId()),
//        ]);
//    }
//
//    public function cashLog(MemberMoneyService $memberMoneyService)
//    {
//        $pageSize = 10;
//        $option = [];
//        $option['order'] = ['id', 'desc'];
//        $option['where'] = [];
//
//        if (RequestHelper::isPost()) {
//            $page = intval(Input::get('page', 1));
//            $paginateData = $memberMoneyService->paginateCash($this->memberUserId(), $page, $pageSize, $option);
//            $html = null;
//            if (!empty($paginateData['records'])) {
//                $html = $this->_viewRender('member.money.cashLogItems', [
//                    'records' => $paginateData['records'],
//                ]);
//            }
//            return Response::send(0, null, [
//                'html' => $html,
//                'page' => $page,
//                'pageSize' => $pageSize,
//                'total' => $paginateData['total'],
//            ]);
//        }
//
//        $page = 1;
//        $paginateData = $memberMoneyService->paginateCash($this->memberUserId(), $page, $pageSize, $option);
//        return $this->_view('member.money.cashLog', [
//            'total' => $paginateData['total'],
//            'page' => $page,
//            'pageSize' => $pageSize,
//            'records' => $paginateData['records'],
//        ]);
//    }
}