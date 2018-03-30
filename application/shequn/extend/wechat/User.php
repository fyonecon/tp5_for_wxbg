<?php
/**
 * Author: 178417451@qq.com
 * Time: 2016/8/2 9:58
 */

namespace app\shequn\extend\wechat;


class User
{
    private $app_id;
    private $app_secret;
    private $jsonname;

    /**
     * User constructor.
     * @param $app_id
     * @param $app_secret
     * @param string $json_file
     * @param string $token
     */
    public function __construct($app_id, $app_secret, $jsonname = 0)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->jsonname = $jsonname;
        $this->http = new Http();
    }

    /**
     * 获取授权后的用户信息
     * @param string $scope
     * @param string $redirect_uri
     * @param string $state
     * @return bool|mixed
     */
    public function getAuth($scope = "snsapi_userinfo", $redirect_uri = '', $state = 'STATE')
    {
        if(isset($_GET['code'])){
            $token = $this->get_access_token($_GET['code']);
            if(!isset($token['access_token'])){
                header("location:".$redirect_uri);
                exit();
            }
            if($scope == "snsapi_base"){
                return $token['openid'];
            }else{
                $userinfo = $this->getUserinfo($token['access_token'], $token['openid']);
                return $userinfo;
            }
        }else{
            $this->get_authorize_url($scope, $redirect_uri, $state);
        }
        return FALSE;
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @param $open_id
     * @return mixed
     */
    public function getUserinfo($access_token, $open_id)
    {
        $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
        $info_data = $this->http->url($info_url);
        return json_decode($info_data, TRUE);
    }

    /**
     * 获取授权链接
     * @param string $scope
     * @param string $redirect_uri
     * @param string $state
     */
    public function get_authorize_url($scope, $redirect_uri = '', $state = 'STATE')
    {
        $redirect_uri = $redirect_uri ? $redirect_uri : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $redirect_uri = urlencode($redirect_uri);
        $redirect_uri = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header("Location:{$redirect_uri}");
        exit();
    }

    /**
     * 获取授权token
     * @param string $code
     * @return bool|mixed
     */
    public function get_access_token($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http->url($token_url);
        return json_decode($token_data, TRUE);
    }
}