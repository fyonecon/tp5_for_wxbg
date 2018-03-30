<?php
/**
 * Author: 178417451@qq.com
 * Time: 2017/10/24 14:28
 */

namespace app\shequn\controller;


use app\shequn\extend\MakeImg;
use app\shequn\model\Qun;
use app\shequn\model\QunBlack;
use app\shequn\model\QunBlackip;
use app\shequn\model\QunEwm;
use app\shequn\model\QunRukou;
use app\shequn\model\QunUser;
use think\Controller;

class Index extends Controller
{
    protected $qun;
    protected $openid;
    protected $user;

    public function __construct()
    {
        parent::__construct();
        if (!input('i')) {
            echo "活动已经结束";
            exit();
        }
        $id = deXcrypt(input('i'));
        if (!$id) {
            echo "活动已经结束!";
            exit();
        }
        $quns = new Qun();
        $this->qun = $quns->get_qun($id);
        if ($this->qun['get_user']) {
            $this->user = getuser();
            $this->openid = $this->user['openid'];
        } else {
            $this->openid = getopenid();
            $this->user = [
                'openid' => $this->openid,
                'nickname' => '',
                'headimgurl' => ''
            ];
        }
    }

    /**
     * 显示群二维码
     * @param $id
     * @return \think\response\View
     */
    public function index()
    {
        $img_url = $this->qun['default_img']; //展现的二维码
        $touch_url = ""; //长按URL
        $tongji = $this->qun['tongji2']; //黑名单统计代码

        //ip
        $getip = getips();
        $ip = $getip['ip'];
        $arr_ip = explode(".", $ip);
        $is_gwip = 0; //国外ip
        if(count($arr_ip) == 4){
            $ipd = $arr_ip[0] . "." . $arr_ip[1] . "." . $arr_ip[2];
            if($arr_ip[0] > 61 && $arr_ip[0] < 100){
                $is_gwip = 1;
            }
        }else{
            $ipd = "none";
        }
        $ipds = explode(',', $this->qun['black_ip']);
        //区域
        $area = GetIpAddress($ip) ? GetIpAddress($ip) : "none";
        $areas = array_merge(explode(',', $this->qun['black_area']), ["美国", "韩国", "日本", "加拿大", "俄罗斯", "英国", "德国", "法国", "泰国", "澳大利亚", "西班牙"]);

        //昵称
        $nickname = $this->user['nickname'] ? $this->user['nickname'] : "none";
        $nicknames = array_merge(explode(',', $this->qun['black_name']), ["课程助理", "课程老师", "群主", "群助理", "管理员"]);

        //入口
        $rokou = input('r');
        $rukous = new QunRukou();
        $qun_rukous = $rukous->get_qun_rukou($this->qun['id']);

        //ua
        $ua = getua();
        if (
            in_array($rokou, $qun_rukous) //入口存在
            && $ua['sys']  //能获取设备
            && $ua['net']  //能获取网络
            && !in_array($area, $areas) //区域未加入黑名单
            && !in_array($ipd, $ipds) //ip段未加入黑名单
            && !$is_gwip //非国外ip
            && !in_array($nickname, $nicknames) //昵称正常
        ) {
            $qun_black = new QunBlack();
            $black_user = $qun_black->getOne(['openid' => $this->openid], ['openid']);
            $qun_blackip = new QunBlackip();
            $black_time = date("Y-m-d H:i:s", time() - 86400 * 3); //最近3天
            $black_ip = $qun_blackip->getOne(['ip' => $ip, "create_time" => ['>=', $black_time]], ['ip', 'create_time']);
            if (!$black_user && !$black_ip) { //用户没有被拉黑
                $img_url = $this->getimg(); //展现的二维码
                $touch_url = url("touch", ['i' => input('i')]); //长按URL
                $tongji = $this->qun['tongji1']; //白名单统计代码
            }
        }
        return view('index', compact('img_url', 'touch_url', 'tongji'));
    }

    /**
     * 请求用户所属二维码图片
     * @return bool|string
     */
    protected function getimg()
    {
        $qun_users = new QunUser();
        $user_ewm = $qun_users->user_ewm($this->qun['id'], $this->user); //获取用户所属二维码
        //用户打开二维码次数加1
        $qun_users->add_open($this->qun['id'], $user_ewm, $this->openid);
        $ewm = enXcrypt($user_ewm);
        $ewm = "/" . config('shequn_file.dir') . "/" . $this->qun['key'] . "/" . config('shequn_file.uploads') . "/{$ewm}.jpg";
        if (!is_file("." . $ewm)) {
            $img = new MakeImg();
            $ewm = $img->makeimg($this->qun, $user_ewm);
        }
        $this->change();
        return $ewm;
    }

    /**
     * 长按二维码
     */
    public function touch()
    {
        $qun_users = new QunUser();
        $user_ewm = $qun_users->user_ewm($this->qun['id'], $this->user); //获取用户所属二维码
        $qun_user = $qun_users->getOne(['qun_id' => $this->qun['id'], 'openid' => $this->openid], ['touchs']);
        if (!$qun_user['touchs']) {//用户是否长按过
            //二维码长按人次加1
            $qun_ewms = new QunEwm();
            $qun_ewms->add_touch($this->qun['id'], $user_ewm);
            $this->change();
        }
        //用户长按二维码次数加1
        $qun_users->add_touch($this->qun['id'], $user_ewm, $this->openid);
    }

    /**
     * 自动切群
     */
    public function change()
    {
        $quns = new Qun();
        $times = $quns->ewm($this->qun['id']);//获取当前二维码打开人次和长按人次
        if (($this->qun['maxopen'] && $this->qun['maxopen'] <= $times['opens']) || ($this->qun['maxtouch'] && $this->qun['maxtouch'] <= $times['touchs'])) {
            //切换到下一个二维码
            $quns->change_now_ewm($this->qun['id']);
        }
    }
}