<?php

namespace app\shequn\model;
class QunEwm extends Base
{
    /**
     * 二维码打开人次加1
     * @param $qun_id //群项目id
     * @param $ewm_id //群二维码id
     * @return int|true
     */
    public function add_open($qun_id, $ewm_id)
    {
        return $this->inc(['qun_id' => $qun_id, 'ewm_id'=>$ewm_id], 'opens', 1);
    }

    /**
     * 二维码长按人次加1
     * @param $qun_id //群项目id
     * @param $ewm_id //群二维码id
     * @return int|true
     */
    public function add_touch($qun_id, $ewm_id)
    {
        return $this->inc(['qun_id' => $qun_id, 'ewm_id'=>$ewm_id], 'touchs', 1);
    }

    /**
     * 获取二维码打开次数和长按次数
     * @param $qun_id //群项目id
     * @param $ewm_id //群二维码id
     * @return array|null
     */
    public function ewm($qun_id, $ewm_id)
    {
        $res = $this->getOne(['qun_id' => $qun_id, 'ewm_id'=>$ewm_id], ['opens', 'touchs']);
        return $res;
    }
}