<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/24
 * Time: 下午8:57
 */

namespace common\tools;


class WxAppTpl
{
    const USER_REGISTER = "";

    const COMPANY_APPLY = "";
    const COMPANY_PASS = "";
    const COMPANY_FORBID = "";

    const JOB_APPLY = "";
    const JOB_PASS = "";
    const JOB_REFUSE = "";
    const JOB_END = "";


    const CLOCK_NOTICE = "";

    const TIME_UP = "";
    const TIME_UP_PASS = "";
    const TIME_UP_REFUSE = "";

    public static function Send($tplId, $openId, $formId, $data, $page, $keyword) {
        $tplData = [];
        for ($j = 1; $j <= count($data); $j++) {
            $tplData[ "keyword" . $j ] = $data[ $j - 1 ];
        }
        $accessToken = WxApp::getAccessToken();
        return WxApp::sendTpl($accessToken, $openId, $tplId, $tplData, $page, $formId, $keyword);
    }

}