<?php

namespace app\shequn\model;
class QunRukou extends Base
{
    /**
     * 根据id获取入口落地数据
     * @param $id
     * @return array|bool|float|int|mixed|null|string
     */
    public function get_rukou($id)
    {
        $key = "rukou_{$id}";
        if (!$this->mc->get($key)) {
            $res = $this->getOne(['id' => $id], ['host200', 'qun_id', 'rukou', 'shunturl', 'shuntnum']);
            $this->mc->set($key, $res, 86400 * 7);
            return $res;
        }
        return $this->mc->get($key);
    }

    /**
     * 清除入口落地数据
     * @param $id
     * @return bool
     */
    public function del_rukou($id)
    {
        $key = "rukou_{$id}";
        return $this->mc->delete($key);
    }


    /**
     * 获取允许进群的入口
     * @param $qun_id
     * @return array|bool|float|int|mixed|string
     */
    public function get_qun_rukou($qun_id)
    {
        $key = "qun_rukou_{$qun_id}";
        if (!$this->mc->get($key)) {
            $res = $this->getAll(['qun_id' => $qun_id, 'status' => 1], ['rukou']);
            $arr = [];
            foreach ($res as $v) {
                $arr[] = $v['rukou'];
            }
            $this->mc->set($key, $arr, 86400 * 7);
            return $arr;
        }
        return $this->mc->get($key);
    }

    /**
     * 清除获取允许进群的入口数据
     * @param $qun_id
     * @return bool
     */
    public function del_qun_rukou($qun_id)
    {
        $key = "qun_rukou_{$qun_id}";
        return $this->mc->delete($key);
    }
}