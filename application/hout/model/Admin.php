<?php

namespace app\hout\model;



class Admin extends Base
{
    public function _getList($where = "", $feild = "", $page = 30, $order = ['id'=>'desc'])
    {
        $res = $this->with('admingroup')->where($where)->field($feild)->order($order)->paginate($page,false,['query' => request()->param()]);
        if($res){
            $data = $res->toArray();
            $data['page'] = $res->render();
        }else{
            $data = null;
        }
        return $data;
    }

    public function _getOne($where = "", $feild = "")
    {
        $res = $this->with('admingroup')->where($where)->field($feild)->find();
        if($res){
            return $res->toArray();
        }else{
            return null;
        }
    }

    public function admingroup()
    {
        return $this->hasOne('AdminGroup', 'id', 'group')->field(['id','name']);
    }
}
