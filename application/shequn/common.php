<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
if (!function_exists('getopenid')) {
    /**
     * 获取用户openid
     * @return mixed
     */
    function getopenid()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $authurl = "http://".config('wechatauth.host')."/shequn/getinfo/openid?url={$url}";
        $authkey = "oni_32";
        if (!cookie($authkey)) {
            $client = input("client", 0);
            if ($client) {
                $openid = deXcrypt($client);
                if (strlen($openid) == 28) {
                    cookie($authkey, $client, 86400 * 3);
                } else {
                    header("location:{$authurl}");
                    exit();
                }
            } else {
                header("location:{$authurl}");
                exit();
            }
        }
        return deXcrypt(cookie($authkey));
    }
}


if (!function_exists('getuser')) {
    /**
     * 获取用户openid
     * @return mixed
     */
    function getuser()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $authurl = "http://".config('wechatauth.host')."/shequn/getinfo/user?url={$url}";
        $authkey = "onu_32";
        if (!cookie($authkey)) {
            $client = input("client", 0);
            if ($client) {
                $openid = deXcrypt($client);
                if (strlen($openid) == 28) {
                    $user = [
                        'a' => $client,
                        'e' => input('x'),
                        'q' => input('y')
                    ];
                    cookie($authkey, $user, 86400 * 3);
                } else {
                    header("location:{$authurl}");
                    exit();
                }
            } else {
                header("location:{$authurl}");
                exit();
            }
        }
        $user = cookie($authkey);
        $userinfo = [
            'openid' => deXcrypt($user['a']),
            'nickname' => deXcrypt($user['e']),
            'headimgurl' => deXcrypt($user['q']),
        ];
        return $userinfo;
    }
}

if (!function_exists('getips')) {
    /**
     * 获取ip和代理ip
     * @return mixed
     */
    function getips()
    {
        $forward_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";// get_client_ip();
        if ($forward_ip) {
            $ips['ip'] = $forward_ip;
            $ips['ip2'] = $ip;
        } else {
            $ips['ip'] = $ip;
            $ips['ip2'] = null;
        }
        return $ips;
    }
}

if (!function_exists('getua')) {
    /**
     * 获取微信设备和网络
     * @return mixed
     */
    function getua($ua = "")
    {
        if (!$ua) {
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
        }
        $pre1 = '/^Mozilla\/[^(]+\(([^\)]+).*NetType\/(\w*)/u';
        $pre2 = '/Mozilla\/[^(]+\(.*(Android|iPhone|iPad).*NetType\/(\w*)/u';
        preg_match($pre2, $ua, $res);
        $result["sys"] = isset($res[1]) ? $res[1] : 0;
        $result["net"] = isset($res[2]) ? $res[2] : 0;
        return $result;
    }
}

if (!function_exists('GetIpAddress')) {
    //获取IP所对应的地址
    function GetIpAddress($ip = '')
    {
        if (!$ip) {
            $ips = getips();
            $ip = $ips["ip"];
        }
        $area = "";
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip={$ip}";
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $str = curl_exec($ch);
        curl_close($ch);
        if($str){
            preg_match('#\{.+?\}#', $str, $arr);
            if (isset($arr[0])) {
                $json = json_decode($arr[0], true);
                if (isset($json['city']) && $json['city']) {
                    return $json['city'];
                }
                if (isset($json['province']) && $json['province']) {
                    return $json['province'];
                }
                if (isset($json['country']) && $json['country']) {
                    return $json['country'];
                }
            }
        }
        return $area;
    }
}

if(!function_exists('enXcrypt'))
{
    /**
     * //加密函数
     * @return mixed
     */
    function enXcrypt($str)
    {
        $key = config('secret'); //64 bit
        $x = new \app\shequn\extend\Xcrypt($key, 'cbc', $key);
        return $x->encrypt($str, 'hex');
    }
}

if(!function_exists('deXcrypt'))
{
    /**
     * //解密函数
     * @return mixed
     */
    function deXcrypt($str)
    {
        $key = config('secret'); //64 bit
        $x = new \app\shequn\extend\Xcrypt($key, 'cbc', $key);
        $r = $x->decrypt($str, 'hex');
        if(json_encode($r)){
            return $r;
        }else{
            return '';
        }
    }
}