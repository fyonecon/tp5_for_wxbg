<?php

namespace app\shequn\model;
class QunUser extends Base
{
    /**
     * 获取用户状态
     * @param $openid
     * @return bool|float|int|mixed|string
     */
    public function getuser($openid)
    {
        $res = $this->getOne(['openid' => $openid], ['black']);
        return $res;
    }

    /**
     * 拉黑用户
     * @param $openid
     * @param $ip
     * @param $status
     * @return false|int
     */
    public function black($openid, $ip, $status)
    {
        $this->update(['black' => $status], ['openid' => $openid]);
        $qun_black = new QunBlack();
        if ($status) {
            if ($qun_black->getOne(['openid' => $openid], ['openid'])) {
                return false;
            }
            $data['openid'] = $openid;
            $data['ip'] = $ip;
            $res = $qun_black->save($data);
        } else {
            $res = $qun_black->where(['openid' => $openid])->delete();
        }
        return $res;
    }

    /**
     * 批量拉黑用户
     * @param $ids
     * @param $status
     * @return array|false|string
     */
    public function blackAll($ids, $status)
    {
        $list = array();
        foreach ($ids as $k => $id) {
            $list[$k] = array('id' => $id, 'black' => $status);
        }
        $this->saveAll($list);
        $datas = $this->all($ids);
        $qun_black = new QunBlack();
        $black = array();
        $res = "";
        if ($status) {
            foreach ($datas as $key => $data) {
                $where = array('openid' => $data['openid'], 'ip' => $data['ip']);
                $result = $qun_black->where($where)->find();
                if (!$result) {
                    $black[$key] = array('openid' => $data['openid'], 'ip' => $data['ip']);
                }
            }
            $res = $qun_black->saveAll($black);
        }
        return $res;
    }

    /**
     * 拉黑ip
     * @param $openid
     * @param $ip
     * @param $status
     * @return false|int
     */
    public function blackip($openid, $ip, $status)
    {
        $this->update(['blackip' => $status], ['openid' => $openid]);
        $qun_black = new QunBlackip();
        if ($status) {
            if ($qun_black->getOne(['ip' => $ip], ["ip"])) {
                return false;
            }
            $data['ip'] = $ip;
            $res = $qun_black->save($data);
        } else {
            $res = $qun_black->where(['ip' => $ip])->delete();
        }
        return $res;
    }

    /**
     * 批量拉黑ip
     * @param $ids
     * @param $status
     * @return array|false|string
     */
    public function blackipAll($ids, $status)
    {
        $list = array();
        foreach ($ids as $k => $id) {
            $list[$k] = array('id' => $id, 'blackip' => $status);
        }
        $this->saveAll($list);
        $datas = $this->all($ids);
        $qun_black = new QunBlackip();
        $black = array();
        $res = "";
        if ($status) {
            foreach ($datas as $key => $data) {
                $where = array('ip' => $data['ip']);
                $result = $qun_black->where($where)->find();
                if (!$result) {
                    $black[$key] = array('ip' => $data['ip']);
                }
            }
            $res = $qun_black->saveAll($black);
        }
        return $res;
    }

    /**
     * 获取用户二维码
     * @param $qun_id //群项目id
     * @param $openid
     * @return mixed
     */
    public function user_ewm($qun_id, $user)
    {
        $openid = $user['openid'];
        $key = "user_ewm_{$qun_id}_{$openid}";
        if (!$this->mc->get($key)) {
            $qun_user = $this->getOne(['qun_id' => $qun_id, 'openid' => $openid], ['ewm_id']);
            if ($qun_user) { //当用户存在时
                $uer_ewm = $qun_user['ewm_id'];
            } else { //当用户不存在时
                $quns = new Qun();
                $qun = $quns->get_qun($qun_id);
                $uer_ewm = $quns->get_now_ewm($qun_id);//获取当前二维码id
                $ips = getips();
                if($qun['is_ip']){ //一个ip对应一个二维码
                    $ip = $ips['ip'];
                    $key_ip = "ip_ewm_{$qun_id}_{$ip}";
                    if (!$this->mc->get($key_ip)) { //ip对应的二维码不存在时
                        $qun_ip = $this->getOne(['qun_id' => $qun_id, 'ip' => $ip], ['ewm_id']);
                        if($qun_ip){
                            $uer_ewm = $qun_ip['ewm_id'];
                        }
                        $this->mc->set($key_ip, $uer_ewm, 86400 * 7);
                    }else{ //ip对应的二维码存在时
                        $uer_ewm = $this->mc->get($key_ip);
                    }
                }
                //用户入库
                $data['openid'] = $openid;
                $data['nickname'] = $user['nickname'];
                $data['headimgurl'] = $user['headimgurl'];
                $data['ip'] = $ips['ip'];
                $data['ip2'] = $ips['ip2'];
                $data['area'] = GetIpAddress($ips['ip']);
                $data['ua'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
                $data['rukou'] = input('r');
                $data['qun_id'] = $qun_id;
                $data['ewm_id'] = $uer_ewm;
                $data['opens'] = 0;
                $this->save($data);
                //二维码打开人次加1
                $qun_ewms = new QunEwm();
                $qun_ewms->add_open($qun_id, $uer_ewm);
            }
            $this->mc->set($key, $uer_ewm, 86400 * 7);
            return $uer_ewm;
        }
        return $this->mc->get($key);
    }


    /**
     * 用户打开二维码次数加1
     * @param $qun_id //群项目id
     * @param $ewm_id //群二维码id
     * @param $openid
     * @return int|true
     */
    public function add_open($qun_id, $ewm_id, $openid)
    {
        return $this->inc(['qun_id' => $qun_id, 'ewm_id' => $ewm_id, 'openid' => $openid], 'opens', 1);
    }

    /**
     * 用户长按二维码次数加1
     * @param $qun_id //群项目id
     * @param $ewm_id //群二维码id
     * @param $openid
     * @return int|true
     */
    public function add_touch($qun_id, $ewm_id, $openid)
    {
        return $this->inc(['qun_id' => $qun_id, 'ewm_id' => $ewm_id, 'openid' => $openid], 'touchs', 1);
    }
}