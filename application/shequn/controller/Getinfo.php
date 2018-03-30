<?php

namespace app\shequn\controller;

use app\shequn\extend\wechat\User;
use think\Controller;


class Getinfo extends Controller
{
    protected $auth;
    protected $goback;
    protected $api;
    public function __construct()
    {
        parent::__construct();
        $this->goback = input('url');
        $this->auth = new User(config('wechatauth.app_id'), config('wechatauth.app_secret'));
        $this->api = "http://".config('wechatauth.host')."/shequn/getinfo";
    }

    /**
     * 获取openid
     * @return \think\response\Redirect
     */
    public function openid()
    {
        $key = "hhss_22";
        if (cookie($key)) {
            return redirect($this->geturl($this->goback, cookie($key)));
        } else {
            if (input('code')) {
                $token = $this->auth->get_access_token(input('code'));
                if (!isset($token['access_token']) || !isset($token['openid'])) {
                    return redirect($this->goback);
                }
                $openid = enXcrypt($token['openid']);
                cookie($key, $openid, time() + 86400 * 30);
                return redirect($this->geturl($this->goback, $openid));
            } else {
                $this->auth->get_authorize_url("snsapi_base", "{$this->api}/openid?url={$this->goback}");
            }
        }
    }

    /**
     * 获取用户信息
     * @return \think\response\Redirect
     */
    public function user()
    {
        $key = "sshh_22";
        if (cookie($key)) {
            return redirect($this->geturl($this->goback, cookie($key)));
        } else {
            if (input('code')) {
                $token = $this->auth->get_access_token(input('code'));
                if (!isset($token['access_token'])) {
                    return redirect($this->goback);
                }
                $user = $this->auth->getUserinfo($token['access_token'], $token['openid']);
                if(!isset($user['openid'])){
                    return redirect($this->goback);
                }
                $openid = enXcrypt($user['openid']);
                $nickname = isset($user['nickname'])? enXcrypt($user['nickname']) : "null";
                $headimgurl = isset($user['headimgurl'])? enXcrypt($user['headimgurl']) : "null";
                cookie($key, $openid, time() + 86400 * 30);
                $arr = [$openid, $nickname, $headimgurl];
                return redirect($this->geturls($this->goback, $arr));
            } else {
                $this->auth->get_authorize_url("snsapi_userinfo", "{$this->api}/user?url={$this->goback}");
            }
        }
    }

    /**
     * 拼接参数
     * @param $goback
     * @param $openid
     * @return string
     */
    public function geturl($goback, $str)
    {
        if (strpos($goback, "?")) {
            $par = '&client='.$str;
        } else {
            $par = '?client='.$str;
        }
        $gourl = $goback . $par;
        return $gourl;
    }

    public function geturls($goback, $arr)
    {
        if (strpos($goback, "?")) {
            $par = "&client=$arr[0]&x=$arr[1]&y=$arr[2]";
        } else {
            $par = "?client=$arr[0]&x=$arr[1]&y=$arr[2]";
        }
        $gourl = $goback . $par;
        return $gourl;
    }
}
