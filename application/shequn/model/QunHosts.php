<?php

namespace app\shequn\model;
class QunHosts extends Base
{
    /**
     * 获取域名下拉
     * @param string $id
     * @return string
     */
    public function HostSelect($data, $name = '')
    {
        $options = "<option value='0'>选择域名</option>";
        foreach ($data as $v)
        {
            $options .= "<option value='".$v['name']."'".($v['name']==$name ? ' selected="selected"' : '').">".$v['name']."</option>";
        }
        return $options;
    }
}