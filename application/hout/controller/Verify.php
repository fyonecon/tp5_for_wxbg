<?php

namespace app\hout\controller;
class Verify
{

    public function is_unique($table='',$field='', $like='')
    {
        $where = '';
        if($like){
            $where[$field] = ['like', '%' . trim(input('param')) . '%'];
        }else{
            $where[$field] = trim(input('param'));
        }
        $result = db($table)->where($where)->find();
        echo $result ? "已经存在，请重新输入！" : '{"info":"验证通过！","status":"y"}';
    }

    //重复输入验证
    public function is_verify($table='',$field=''){
        $result = M($table)->where(array($field=>$_POST["param"]))->find();
        echo $result ? "已经存在，请重新输入！" : '{"info":"验证通过！","status":"y"}';
    }
    public function is_like($table='',$field=''){
        $result = M($table)->where($field." like '%".$_POST["param"]."%'")->find();
        echo $result ? "已经存在，请重新输入！" : '{"info":"验证通过！","status":"y"}';
    }
}
