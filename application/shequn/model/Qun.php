<?php

namespace app\shequn\model;
class Qun extends Base
{

    /**
     * 获取群项目参数
     * @param $k
     * @return array|bool|float|int|mixed|null|string
     */
    public function get_qun($id)
    {
        $key = "qun_{$id}";
        if (!$this->mc->get($key)) {
            $res = $this->getOne(['id' => $id], ['id', 'name', 'key', 'maxopen', 'maxtouch', 'rukou', 'bg_img', 'default_img', 'tongji1', 'tongji2', 'controller', 'black_area', 'black_ip', 'black_name', 'get_user', "is_ip"]);
            $this->mc->set($key, $res, 86400 * 7);
            return $res;
        }
        return $this->mc->get($key);
    }

    /**
     * 获取当前二维码
     * @param //群项目id
     * @return mixed
     */
    public function get_now_ewm($id)
    {
        $key = "now_ewm_{$id}";
        if (!$this->mc->get($key)) {
            $res = $this->getOne(['id' => $id], ['now_ewm_id']);
            $ewm_id = $res['now_ewm_id'];
            $this->set_now_ewm($id, $ewm_id);
            return $ewm_id;
        }
        return $this->mc->get($key);
    }

    /**
     * 设置当前二维码
     * @param $id //群项目id
     * @param $ewm_id //二维码id
     */
    public function set_now_ewm($id, $ewm_id)
    {
        $qun_ewms = new QunEwm();
        $qun_ewm = $qun_ewms->getOne(['qun_id' => $id, 'ewm_id' => $ewm_id], ['id']);
        if (!$qun_ewm) {
            //二维码入库
            $data['qun_id'] = $id;
            $data['ewm_id'] = $ewm_id;
            $data['start_time'] = time();
            $qun_ewms->save($data);
        }
        //更新当前二维码
        $this->update(['now_ewm_id'=>$ewm_id], ['id'=>$id]);
        $key = "now_ewm_{$id}";
        $this->mc->set($key, $ewm_id, 86400 * 7);
    }

    /**
     * 切换到下一个二维码
     * @param $id //群项目id
     */
    public function change_now_ewm($id)
    {
        //获取当前二维码
        $now_ewm = $this->get_now_ewm($id);
        //更新当前二维码结束时间
        $qun_ewms = new QunEwm();
        $qun_ewms->update(['end_time' => time()], ['qun_id' => $id, 'ewm_id' => $now_ewm]);
        //设置当前二维码
        $this->set_now_ewm($id, $now_ewm + 1);
    }


    /**
     * 切换到指定二维码
     * @param $id //群项目id
     * @param $ewm_id
     */
    public function change_ewm($id, $ewm_id)
    {
        //获取当前二维码
        $now_ewm = $this->get_now_ewm($id);
        //更新当前二维码结束时间
        $qun_ewms = new QunEwm();
        $qun_ewms->update(['end_time' => time()], ['qun_id' => $id, 'ewm_id' => $now_ewm]);
        //设置当前二维码
        $this->set_now_ewm($id, $ewm_id);
    }

    /**
     * 获取当前二维码打开人次和长按人次
     * @param $id //群项目id
     * @return mixed
     */
    public function ewm($id)
    {
        $now_ewm_id = $this->get_now_ewm($id);
        $qun_ewms = new QunEwm();
        return $qun_ewms->ewm($id, $now_ewm_id);
    }
}