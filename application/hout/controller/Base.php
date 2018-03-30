<?php

namespace app\hout\controller;

use app\hout\model\AdminGroup;
use think\Controller;

class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!session("admin_login")){
            $this->redirect(url('hout/login/index'));
        }
        $addons_dir = APP_PATH;
        $addons = glob($addons_dir.'*', GLOB_ONLYDIR);
        //halt($addons);
        $addons_name = array_map('basename', $addons);
        $menus = [];
        foreach($addons as $v){
            try{
                $cfg = include $v."/config.php";
                if($cfg['menu']){
                    $menus[] = $cfg['menu'];
                }
            }catch (\Exception $e) {

            }

        }
//        halt($menus);
        if(session("admin_login.group") == 1){
            $menu = $menus;
        }else{
            $menu = $this->cheak_menu($menus);
        }
        $this->assign(compact('menu'));
    }

    public function _initialize()
    {

    }

    public function cheak_menu($menus)
    {
        $groups = new AdminGroup();
        $group = $groups->getOne(['id'=>session("admin_login.group")], ['rules']);
        $menu = [];
        $menu_url[] = "hout/index";
        if($menus){
            foreach ($menus as $a => $v){
                if(in_array($a, explode(',',$group['rules']))){
                    $menu[$a] = $v;
                    unset($menu[$a]['menus']);
                    if(isset($v['url'])){
                        list($module, $controller,) = explode('/', strtolower($v['url']));
                        $menu_url[] = $module."/".$controller;
                    }
                }
                if(isset($v['menus']) && $v['menus']){
                    foreach ($v['menus'] as $b => $item){
                        if(in_array($a."_".$b, explode(',',$group['rules']))){
                            $menu[$a]['menus'][] = $item;
                            if(isset($item['url'])){
                                list($module, $controller,) = explode('/', strtolower($item['url']));
                                $menu_url[] = $module."/".$controller;
                            }
                        }
                    }
                }
            }
        }
        //halt($menu_url);
        $rule = strtolower(request()->module().'/'.request()->controller());
        //halt($rule);
        if(!in_array($rule, $menu_url)){
            $this->redirect(url('hout/login/logout'), [], 302, ['msg'=>"没有权限！"]);
        }
        return $menu;
    }

}
