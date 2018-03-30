<?php

namespace app\hout\controller;

use app\hout\model\AdminLog;
use think\Controller;
use app\hout\model\Admin;
use app\hout\extend\SysCrypt;

class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 显示
     * @return \think\response\View
     */
    public function index()
    {
        if(session("admin_login")){
            $this->redirect(url('index/index'));
        }
        return view();
    }

    /**
     * 登录
     */
    public function login()
    {
        $admins = new Admin();
        $admin = $admins->getOne(['username'=>input("post.username")], ['id', 'name', 'password', 'username', 'group']);
        if($admin){
            $password = SysCrypt::php_decrypt($admin['password'],config('secret'));
            if(input("post.password") == $password){
                unset($admin['password']);
                session("admin_login", $admin);
                $this->redirect(url('index/index'));
            }else{
                $this->redirect(url('hout/login/index'), [], 302, ['msg'=>"密码错误！"]);
            }
        }else{
            $this->redirect(url('hout/login/index'), [], 302, ['msg'=>"用户不存在！"]);
        }
    }

    public function logout()
    {
        session(null);
        $this->redirect(url('hout/login/index'));
    }


}
