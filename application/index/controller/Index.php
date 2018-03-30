<?php
namespace app\index\controller;

use app\hout\extend\SysCrypt;
use app\shequn\extend\MemcacheSASL;
use think\Controller;
use think\Db;

class Index extends Controller
{

    public function index()
    {
        return view('index');
    }

    public function m()
    {
        $mc = new MemcacheSASL();
        $mc->addServer(config("memcache.hostname"), config("memcache.port"));
        if(config("memcache.username") && config("memcache.password")){
            $mc->setSaslAuthData(config("memcache.username"), config("memcache.password"));
            $mc->setSaveHandler();
        }
        $mc->set("qq", 321321);
        echo $mc->get("qq");
    }

    public function de()
    {
        $password = SysCrypt::php_decrypt("", "csyg");
        echo $password;
        $id = input('id');
        halt( is_numeric($id));
    }

}
