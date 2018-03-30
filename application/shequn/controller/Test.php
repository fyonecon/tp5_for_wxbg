<?php
/**
 * Author: 178417451@qq.com
 * Time: 2017/10/24 12:38
 */

namespace app\shequn\controller;

use app\shequn\extend\MemcacheSASL;
use app\hout\controller\Base;
use app\shequn\model\Qun;
use app\shequn\model\QunRukou;

class Test extends Base
{
    public function de()
    {
        $a = '1001';
//        $a = '1534';
        echo enXcrypt($a),"<br>";
        echo deXcrypt("5098b94670940ee8");
    }

    public function mc($id = 1, $rk = 0)
    {
        $mc = new MemcacheSASL();
        $mc->addServer(config("memcache.hostname"), config("memcache.port"));
        if(config("memcache.username") && config("memcache.password")){
            $mc->setSaslAuthData(config("memcache.username"), config("memcache.password"));
            $mc->setSaveHandler();
        }
        $quns = new Qun();
        $rukous = new QunRukou();
        if($rk){
            $rukou = $rukous->get_rukou($rk);
            dump($rukou);
            $qun = $quns->get_qun($rukou['qun_id']);
            $qun_id = enXcrypt($rukou['qun_id']);
            echo $url = "http://{$rukou['host200']}/shequn/{$qun['controller']}/index/i/{$qun_id}/r/{$rukou['rukou']}.html";
        }else{
            $qun = $quns->get_qun($id);
            dump($qun);
            $rukou = $rukous->get_qun_rukou($id);
            dump($rukou);
            echo $get_now_ewm = $quns->get_now_ewm($id);
        }

    }
}