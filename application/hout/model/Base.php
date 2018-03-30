<?php

namespace app\hout\model;

use think\Model;

class Base extends Model
{
    //自动写入时间
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 获取单条数据并返回数组
     * @param string $where
     * @param string $feild
     * @return array|null
     */
    public function getOne($where = "", $feild = "")
    {
        $res = $this->where($where)->field($feild)->find();
        if($res){
            return $res->toArray();
        }else{
            return null;
        }
    }

    /**
     * 获取多条数据
     * @param string $where
     * @param string $feild
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAll($where = "", $feild = "", $order = ['id'=>'desc'])
    {
        $res = $this->where($where)->field($feild)->order($order)->select();
        return $res;
    }

    public function getLimit($where = "", $feild = "", $limit = "0,30", $order = ['id'=>'desc']){
        $res = $this->where($where)->field($feild)->limit($limit)->order($order)->select();
        return $res;
    }

    /**
     * 获取分页
     * @param string $where
     * @param string $feild
     * @param int $page
     * @param array $order
     * @return mixed
     */
    public function getList($where = "", $feild = "", $page = 30, $order = ['id'=>'desc'])
    {
        $res = $this->where($where)->field($feild)->order($order)->paginate($page,false,['query' => request()->param()]);
        if($res){
            $data = $res->toArray();
            $data['page'] = $res->render();
        }else{
            $data = null;
        }
        return $data;
    }


    /**
     * 添加或更新，并反回所有数据
     * @param string $where
     * @param string $data
     * @return array|false|int
     */
    public function addOrUp($where = '', $data = '')
    {
        $res = $this->save($data, $where);
        if($res){
            return $this->toArray();
        }else{
            return $res;
        }
    }

    /**
     * 删除
     * @param $where
     * @return int
     * @throws \think\Exception
     */
    public function del($where)
    {
        return $this->where($where)->delete();
    }

    /**
     * 自增
     * @param $where
     * @param $feild
     * @param $number
     * @return int|true
     * @throws \think\Exception
     */
    public function inc($where, $feild, $number)
    {
        return $this->where($where)->setInc($feild, $number);
    }

    /**
     * 自减
     * @param $where
     * @param $feild
     * @param $number
     * @return int|true
     * @throws \think\Exception
     */
    public function dec($where, $feild, $number)
    {
        return $this->where($where)->setDec($feild, $number);
    }
}
