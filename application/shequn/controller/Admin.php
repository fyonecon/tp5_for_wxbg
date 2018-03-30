<?php

namespace app\shequn\controller;

use app\hout\controller\Base;
use app\shequn\extend\MakeImg;
use app\shequn\model\Qun;
use app\shequn\model\QunBlack;
use app\shequn\model\QunBlackip;
use app\shequn\model\QunEwm;
use app\shequn\model\QunHosts;
use app\shequn\model\QunRukou;
use app\shequn\model\QunUser;

class Admin extends Base
{
    protected $qun;

    public function __construct()
    {
        parent::__construct();
        $this->qun = new Qun();
    }

    /**
     * 项目管理
     * @return \think\response\View
     */
    public function index()
    {
        $title = "项目管理";
        $where = [];
        input('name') && $where['name'] = ['like', '%' . trim(input('name')) . '%'];
        $data = $this->qun->getList($where);
        return view('index', compact('data', 'title'));
    }

    /**
     * 编辑项目
     * @param string $id
     * @return \think\response\View
     */
    public function edit($id = "")
    {
        $title = $id ? '修改项目' : '添加项目';
        $data = $this->qun->getOne(['id' => $id]);
        $url = [];
        if ($id) {
            $rukou = new QunRukou();
            $url = $rukou->getList(["qun_id" => $id]);
        }
        $hosts = new QunHosts();
        $hosts = $hosts->getAll(['status' => 1], ['id', 'name', 'type']);
        return view('edit', compact('title', 'data', 'url', 'hosts'));
    }

    /**
     * 保存项目
     * @param string $id
     * @return \think\response\Redirect
     */
    public function save($id = '')
    {
        $data = input("post.");
        if ($data['key']) {
            $data['key'] = strtolower($data['key']);
        } else {
            $this->error("请填写唯一标识！");
        }

        $data['controller'] = strtolower($data['controller']);
        if (!$id) {
            $qunsa = $this->qun->getOne(['name' => $data['name']]);
            $qunsb = $this->qun->getOne(['key' => $data['key']]);
            if ($qunsa || $qunsb) {
                $this->error("已经存在，重新添加");
            }
        }
        $dir = config('shequn_file.dir');
        //创建目录
        $path = ROOT_PATH . 'public' . DS . $dir . DS . $data['key'];
        $ewm_path = $path . DS . config('shequn_file.ewms');
        if (!file_exists($ewm_path)) {
            mkdir($ewm_path, 0755, true);
        }
        $upload_path = $path . DS . config('shequn_file.uploads');
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        $where = [];
        $id && $where['id'] = $id;

        //背景图
        if (request()->file('bg_img')) {
            $file = request()->file('bg_img');
            $info = $file->move($path, '');
            if ($info) {
                $data['bg_img'] = "/" . $dir . "/" . $data['key'] . "/" . $info->getSaveName();
            }
        } else {
            unset($data['bg_img']);
        }
        //默认图
        if (request()->file('default_img')) {
            $file = request()->file('default_img');
            $info = $file->move($path, '');
            if ($info) {
                $data['default_img'] = "/" . $dir . "/" . $data['key'] . "/" . $info->getSaveName();
            }
        } else {
            unset($data['default_img']);
        }
        unset($data['upimg']);
        $this->qun->addOrUp($where, $data);
        if ($id) {
            $key = "qun_{$id}";
            $this->qun->mc->delete($key);
        }
        return redirect('index');
    }


    /**
     * 上传群二维码
     * @param string $id
     * @return int
     */
    public function ewmuploads($key = "")
    {
        if (!$key) {
            return json(["status" => 0]);
        }
        $dir = config('shequn_file.dir');
        $imgpath1 = ROOT_PATH . 'public' . DS . $dir . DS . $key;
        if (!file_exists($imgpath1)) {
            mkdir($imgpath1, 0755);
        }
        $imgpath2 = ROOT_PATH . 'public' . DS . $dir . DS . $key . DS . config('shequn_file.ewms');
        if (!file_exists($imgpath2)) {
            mkdir($imgpath2, 0755);
        }
        // 获取表单上传文件
        $files = request()->file('upimg');
//        halt(count($files));
        foreach ($files as $file) {
            $info = $file->move($imgpath2, '');
            if ($info) {
                echo $info->getExtension();
                echo $info->getFilename();
            } else {
                echo $file->getError();
            }
        }
    }

    /**
     * 删除项目
     * @param $id
     * @return \think\response\Redirect
     */
    public function del($id)
    {
        $this->qun->del(['id' => $id]);
        $key = "qun_{$id}";
        $this->qun->mc->delete($key);
        return redirect('index');
    }

    /**
     * 当前群统计
     * @param $key
     * @return \think\response\View
     */
    public function tongji($id)
    {
        $qun = $this->qun->get_qun($id);
        $key = $qun['key'];
        $files = glob(ROOT_PATH . "public/" . config('shequn_file.dir') . "/" . $key . "/" . config('shequn_file.ewms') . "/*.jpg");
        $num_arr = [];
        foreach ($files as $k => $v) {
            $ewm = explode("/", $v);
            list($num,) = explode(".", end($ewm));
            $num_arr[] = $num;
        }
        if ($num_arr) {
            sort($num_arr);
//        halt($num_arr);
            $qun_ewms = new QunEwm();
            $ewms = $qun_ewms->getLimit(['qun_id' => $qun['id']], ['id', 'ewm_id', 'start_time', 'end_time', 'opens', 'touchs', 'imgs', 'members'], "0,300", ['id' => 'desc']);
            $ewm_arr = [];
            foreach ($ewms as $k => $v) {
                if ($v['end_time']) {
                    $time = $v['end_time'] - $v['start_time'];
                    $time = $this->secondToDate($time);
                } else {
                    $time = '';
                }
                $ewm_arr[$v['ewm_id']]['id'] = $v['id'];
                $ewm_arr[$v['ewm_id']]['ewm_id'] = $v['ewm_id'];
                $ewm_arr[$v['ewm_id']]['start_time'] = date("Y-m-d H:i:s", $v['start_time']);
                $ewm_arr[$v['ewm_id']]['end_time'] = $v['end_time'] ? date("Y-m-d H:i:s", $v['end_time']) : "";
                $ewm_arr[$v['ewm_id']]['time'] = $time;
                $ewm_arr[$v['ewm_id']]['opens'] = $v['opens'];
                $ewm_arr[$v['ewm_id']]['touchs'] = $v['touchs'];
//              $ewm_arr[$v['ewm_id']]['lost'] = round(($v['opens'] - $v['touchs'])/$v['opens'],2)*100 . "%";
                $ewm_arr[$v['ewm_id']]['imgs'] = $v['imgs'];
                $ewm_arr[$v['ewm_id']]['members'] = $v['members'];
            }
//          halt($ewm_arr);
            $data = [];
            foreach ($num_arr as $k => $name) {
                if (isset($ewm_arr[$name])) {
                    $data[$name] = $ewm_arr[$name];
                } else {
                    $data[$name] = [
                        'id' => 0,
                        'ewm_id' => $name,
                        'start_time' => '',
                        'end_time' => '',
                        'time' => '',
                        'opens' => '',
                        'touchs' => '',
                        'lost' => '',
                        'imgs' => 0,
                        'members' => 0,
                        'sc' => ''
                    ];
                }
            }
        } else {
            $data = [];
        }
        $now_ewm_id = $this->qun->get_now_ewm($qun['id']);
        $title = $qun['name'];
        $qun_id = $qun['id'];
//        halt($data);
        return view('tongji', compact('title', 'data', 'now_ewm_id', 'qun_id'));
    }


    /**
     * 切群
     * @param $qun_id
     * @param $ewm_id
     * @return \think\response\Redirect
     */
    public function change($qun_id, $ewm_id)
    {
        //切群
        $this->qun->change_ewm($qun_id, $ewm_id);
        //生成群二维码
        $qun = $this->qun->get_qun($qun_id);
        $img = new MakeImg();
        $ewm = $img->makeimg($qun, $ewm_id);
        return json(['status' => 1]);
    }

    /**
     * 生成群二维码
     * @param $qun_id ; 群id
     * @param $ewm_id ; 二维码id
     * @param int $is ; 是否重新生成
     */
    public function img($qun_id, $ewm_id, $is = 0)
    {
        $qun = $this->qun->get_qun($qun_id);
        if ($is) {
            $img = new MakeImg();
            $ewm = $img->makeimg($qun, $ewm_id);
        } else {
            $key = $qun['key'];
            $ewm = enXcrypt($ewm_id);
            $ewm = "/" . config('shequn_file.dir') . "/" . $key . "/" . config('shequn_file.uploads') . "/{$ewm}.jpg";
            if (!is_file("." . $ewm)) { //二维码不存在时
                return "<b style='font-size: 50px;'>二维码不存在</b>";
            }
        }
        echo "<img src='$ewm'>";
    }

    /**
     * 用户管理
     * @return \think\response\View
     */
    public function user()
    {
        $title = "用户管理";
        $where = [];
        $order = input('order') ? trim(input('order')) : "update_time";
        input('qun_id') && $where['qun_id'] = trim(input('qun_id'));
        input('ewm_id') && $where['ewm_id'] = trim(input('ewm_id'));
        if (trim(input('openid'))) {
            if (strlen(trim(input('openid'))) == 28) {
                $openid = trim(input('openid'));
            } else {
                $openid = deXcrypt(trim(input('openid')));
            }
            $where['openid'] = $openid;
        }
        input('black') && $where['black'] = trim(input('black'));
        input('ip') && $where['ip'] = trim(input('ip'));
        input('ip2') && $where['ip2'] = trim(input('ip2'));
        input('area') && $where['area'] = trim(input('area'));
        input('nickname') && $where['nickname'] = ['like', '%' . trim(input('nickname')) . '%'];
        input('ua') && $where['ua'] = trim(input('ua'));
        input('rukou') && $where['rukou'] = trim(input('rukou'));
        input('opens') && $where['opens'] = ['>=', trim(input('opens'))];
        input('touchs') && $where['touchs'] = ['>=', trim(input('touchs'))];
        input('begin') && $where['update_time'] = ['>=', input('begin')];
        input('end') && $where['update_time'] = ['<=', input('end')];
        if (input('begin') && input('end')) {
            $where['update_time'] = ['BETWEEN', [input('begin'), input('end')]];
        }
        $qun_users = new QunUser();
        $data = $qun_users->getList($where, ["id", "openid", "nickname", "headimgurl", "ip", "ip2", "area", "ua", "rukou", "qun_id", "ewm_id", "opens", "touchs", "black", "blackip", "create_time", "update_time"], 30, [$order => 'desc']);
//        halt($data);
        return view('user', compact('data', 'title'));
    }

    /**
     * 拉黑用户
     * @param $openid
     * @param $status
     * @return \think\response\Redirect
     */
    public function black($openid, $ip, $status)
    {
        $qun_users = new QunUser();
        $res = $qun_users->black($openid, $ip, $status);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 批量拉黑用户
     * @param $openid
     * @param $status
     * @return \think\response\Redirect
     */
    public function blackAll($ids, $status)
    {
        $qun_users = new QunUser();
        $res = $qun_users->blackAll($ids, $status);
        if ($res) {
            return json(['status' => 1]);
        }
    }

    /**
     * 拉黑ip
     * @param $openid
     * @param $status
     * @return \think\response\Redirect
     */
    public function blackip($openid = "", $ip, $status)
    {
        $qun_users = new QunUser();
        $res = $qun_users->blackip($openid, $ip, $status);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 批量拉黑ip
     * @param $openid
     * @param $status
     * @return \think\response\Redirect
     */
    public function blackipAll($ids, $status)
    {
        $qun_users = new QunUser();
        $res = $qun_users->blackipAll($ids, $status);
        if ($res) {
            return json(['status' => 1]);
        }
    }

    /**
     * 黑名单列表
     * @return \think\response\View
     */
    public function blacks()
    {
        $title = "黑名单";
        $where = [];
        if (trim(input('openid'))) {
            if (strlen(trim(input('openid'))) == 28) {
                $openid = trim(input('openid'));
            } else {
                $openid = deXcrypt(trim(input('openid')));
            }
            $where['openid'] = $openid;
        }
//        input('openid') && $where['openid'] = trim(input('openid'));
        input('ip') && $where['ip'] = trim(input('ip'));
        $qun_blacks = new QunBlack();
        $data = $qun_blacks->getList($where, "", 30, $order = ['update_time' => 'desc']);
        return view('blacks', compact('data', 'title'));
    }

    /**
     * 黑ip列表
     * @return \think\response\View
     */
    public function blackips()
    {
        $title = "ip黑名单";
        $where = [];
        $order = input('order') ? trim(input('order')) : "id";
        input('ip') && $where['ip'] = ['like', trim(input('ip')) . '%'];
        $qun_blacks = new QunBlackip();
        $data = $qun_blacks->getList($where, "", 30, [$order => 'desc']);
        return view('blackips', compact('data', 'title'));
    }

    /**
     * 社群统计
     * @return \think\response\View
     */
    public function ewm()
    {
        $title = "社群统计";
        $where = [];
        input('qun_id') && $where['qun_id'] = trim(input('qun_id'));
        input('begin') && $where['start_time'] = ['BETWEEN', [strtotime(input('begin')), strtotime(input('begin')) + 86400]];
        input('end') && $where['start_time'] = ['<=', strtotime(input('end'))];
        if (input('begin') && input('end')) {
            $where['start_time'] = ['BETWEEN', [strtotime(input('begin')), strtotime(input('end'))]];
        }
        input('today') && $where['start_time'] = ['BETWEEN', [strtotime(date("Y-m-d 00:00:00")), time()]];
        $qun_ewms = new Qunewm();
        $data = $qun_ewms->getList($where);
        $ewm = [];
        foreach ($data['data'] as $k => $v) {
            if ($v['end_time']) {
                $time = $v['end_time'] - $v['start_time'];
                $time = $this->secondToDate($time);
            } else {
                $time = '';
            }
            $ewm[$k]['qun_id'] = $v['qun_id'];
            $ewm[$k]['ewm_id'] = $v['ewm_id'];
            $ewm[$k]['start_time'] = date("Y-m-d H:i:s", $v['start_time']);
            $ewm[$k]['end_time'] = $v['end_time'] ? date("Y-m-d H:i:s", $v['end_time']) : "";
            $ewm[$k]['time'] = $time;
            $ewm[$k]['opens'] = $v['opens'];
            $ewm[$k]['touchs'] = $v['touchs'];
        }
        $quns = $this->qun->getAll();
        return view('ewm', compact('data', 'ewm', 'quns', 'title'));
    }

    /**
     * 更新二维码截图量和人数
     * @return int
     */
    public function update()
    {
        $ewms = new QunEwm();
        $id = input('id');
        $field = input('field');
        $where['id'] = $id;
        $data[$field] = input('value');
        $res = $ewms->update($data, $where);
        $status = 1;
        if (!$res) {
            $status = 0;
        }
        return $status;
    }


    /**
     * 时间戳转年月日
     * @param $time
     * @return bool|string
     */
    private function secondToDate($time)
    {
        if (is_numeric($time)) {
            $value = array(
                "years" => 0, "days" => 0, "hours" => 0,
                "minutes" => 0, "seconds" => 0,
            );
            if ($time >= 31556926) {
                $value["years"] = floor($time / 31556926);
                $time = ($time % 31556926);
            }
            if ($time >= 86400) {
                $value["days"] = floor($time / 86400);
                $time = ($time % 86400);
            }
            if ($time >= 3600) {
                $value["hours"] = floor($time / 3600);
                $time = ($time % 3600);
            }
            if ($time >= 60) {
                $value["minutes"] = floor($time / 60);
                $time = ($time % 60);
            }
            $value["seconds"] = floor($time);
            //return (array) $value;
            $t = "";
            $value["years"] && $t .= $value["years"] . "年";
            $value["days"] && $t .= $value["days"] . "天";
            $value["hours"] && $t .= $value["hours"] . "时";
            $value["minutes"] && $t .= $value["minutes"] . "分";
            $value["seconds"] && $t .= $value["seconds"] . "秒";
            Return $t;

        } else {
            return (bool)FALSE;
        }
    }

    /**
     * 域名管理
     * @return \think\response\View
     */
    public function hosts()
    {
        $title = "域名管理";
        $where = [];
        input('name') && $where['name'] = ['like', '%' . trim(input('name')) . '%'];
        $hosts = new QunHosts();
        $data = $hosts->getList($where);
        return view('hosts', compact('data', 'title'));
    }

    public function hostsadd()
    {
        $data = input('post.');
        $hosts = new QunHosts();
        $res = $hosts->save($data);
        $status = 1;
        if (!$res) {
            $status = 0;
        }
        return $status;
    }

    public function hostsupdate()
    {

        $id = input('id');
        $field = input('field');
        $where['id'] = $id;
        $data[$field] = input('value');
        $hosts = new QunHosts();
        $res = $hosts->update($data, $where);
        $status = 1;
        if (!$res) {
            $status = 0;
        }
        return $status;
    }

    public function hostsdel()
    {
        $id = input('id');
        $hosts = new QunHosts();
        $res = $hosts->del(['id' => $id]);
        $status = 1;
        if (!$res) {
            $status = 0;
        }
        return $status;
    }


    /**
     * 添加入口
     * @return int
     */
    public function rukouadd()
    {
        $data = input('post.');
        if(!$data['host301'] || !$data['host200'] || !$data['rukou']){
            return 0;
        }
        $data['rukou'] = strtolower($data['rukou']);
        $rukou = new QunRukou();
        $res = $rukou->save($data);
        if (!$res) {
            return 0;
        } else {
            $rukou->del_qun_rukou($data['qun_id']);
            return 1;
        }
    }

    /**
     * 更新入口
     * @return int
     */
    public function rukouupdate()
    {

        $id = input('id');
        $field = input('field');
        $where['id'] = $id;
        $data[$field] = input('value');
        $rukou = new QunRukou();
        $res = $rukou->update($data, $where);
        $status = 1;
        if (!$res) {
            $status = 0;
        } else {
            $qun = $rukou->get_rukou($id);
            $rukou->del_qun_rukou($qun['qun_id']);
            $rukou->del_rukou($id);
        }
        return $status;
    }

    /**
     * 删除入口
     * @return int
     */
    public function rukoudel()
    {
        $id = input('id');
        $rukou = new QunRukou();
        $res = $rukou->del(['id' => $id]);
        $qun = $rukou->get_rukou($id);
        $rukou->del_qun_rukou($qun['qun_id']);
        $status = 1;
        if (!$res) {
            $status = 0;
        }
        return $status;
    }

    /**
     * 获取入口数量
     * @return int
     */
    public function rukougetnum()
    {
        $id = input('id');
        $rukou = input('rukou');
        $qun_users = new QunUser();
        $data = $qun_users->where(['qun_id' => $id, 'rukou' => $rukou])->count('id');
        $status = [
            "nums" => 0
        ];
        if ($data) {
            $rukous = new QunRukou();
            $res = $rukous->update(['nums' => $data], ['qun_id' => $id, 'rukou' => $rukou]);
            if($res){
                $status = [
                    "nums" => $data
                ];
            }
        }
        return json($status);
    }

    /**
     * mc查看
     * @param int $id
     * @param int $rk
     */
    public function mc($id = 1, $rk = 0)
    {
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
