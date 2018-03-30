<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    //后台菜单配置
    'menu' => [
        'name' => '社群管理',
        'icon' => 'fa-university',
        'menus' => [
            [
                'name' => '项目管理',
                'url' => 'shequn/Admin/index',
                'icon' => 'fa-comment',
            ],
            [
                'name' => '用户管理',
                'url' => 'shequn/Admin/user',
                'icon' => 'fa-user-o',
            ],
            [
                'name' => '用户黑名单',
                'url' => 'shequn/Admin/blacks',
                'icon' => 'fa-user',
            ],
            [
                'name' => 'ip黑名单',
                'url' => 'shequn/Admin/blackips',
                'icon' => 'fa-user',
            ],
            [
                'name' => '数据统计',
                'url' => 'shequn/Admin/ewm',
                'icon' => 'fa-database',
            ],
            [
                'name' => '域名管理',
                'url' => 'shequn/Admin/hosts',
                'icon' => 'fa-internet-explorer',
            ],
        ],
    ],
    // 应用调试模式
    'app_debug'              => false,
    // 应用Trace
    'app_trace'              => false,

    //----------------------------------------------项目配置---------------------------------------------------

    //memcache配置
    'memcache'              =>[
        'hostname'  => '',
        'port'      => 11211,
        'username'  =>'',
        'password'  =>''
    ],

    //微信授权
    'wechatauth'              =>[ 
        'app_id'    => '',
        'app_secret'=> '',
        'host'      => ''
    ],

    //社群图片目录
    'shequn_file'           => [
        'dir'        => 'qgl',   //项目目录
        'ewms'       => 'qewm', //二维码上传目录
        'uploads'    => 'ul'       //二维码合成目录
    ],

    //密钥
    'secret' => 'beita123', //字节长度8/16/32
];
