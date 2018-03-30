<?php
/**
 * Author: 178417451@qq.com
 * Time: 2016/8/2 10:06
 */

namespace app\shequn\extend\wechat;
use think\Log;

class Http
{
    public function url($url, $method = "GET", $postfields = null, $headers = array(), $debug = true)
    {
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        curl_close($ci);
        $code = json_decode($response, true);
        if($debug && isset($code['errcode']) && $code['errcode'] != 0)
        {
            Log::error($code);
        }
        return $response;
    }
}