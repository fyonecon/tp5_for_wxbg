<?php
/**
 * Author: 178417451@qq.com
 * Time: 2017/11/5 10:41
 */

namespace app\shequn\controller;

use app\shequn\model\Qun;
use app\shequn\model\QunRukou;

class Go
{
    /**
     * 跳转
     * @param $i
     */
    public function index($i)
    {
        $id = deXcrypt($i);
        $rukous = new QunRukou();
        $rukou = $rukous->get_rukou($id);
        $quns = new Qun();
        $qun = $quns->get_qun($rukou['qun_id']);
        $qun_id = enXcrypt($rukou['qun_id']);
        $url = "http://{$rukou['host200']}/shequn/{$qun['controller']}/index/i/{$qun_id}/r/{$rukou['rukou']}.html";

        if(isset($rukou['shunturl']) && $rukou['shunturl'] && isset($rukou['shuntnum']) && $rukou['shuntnum']){ //分流
            $n = mt_rand(1, 10);
            if ($n <= $rukou['shuntnum']) {
                header("location:{$rukou['shunturl']}");
            } else {
                header("location:{$url}");
            }
        }else{
            header("location:{$url}");
        }
    }

    /**
     * 获取openid
     * @return mixed
     */
    public function o()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $authurl = "http://".config('wechatauth.host')."/shequn/getinfo/openid?url={$url}";
        $client = input("client", 0);
        if ($client) {
            return deXcrypt($client);
        } else {
            header("location:{$authurl}");
            exit();
        }

    }
}