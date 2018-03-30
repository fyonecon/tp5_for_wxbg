<?php
namespace app\shequn\extend;
use app\shequn\model\Qun;

/**
 * Author: 178417451@qq.com
 * Time: 2017/5/18 17:03
 */
class MakeImg
{
    public function makeimg($qun, $ewm_id)
    {
        //背景
        $bg_im = imagecreatefrompng("./" . $qun['bg_img']);
        //二维码
        $ewm = "./" . config('shequn_file.dir') . "/" . $qun['key'] . "/".config('shequn_file.ewms')."/{$ewm_id}.jpg";
        if (!is_file($ewm)) {
            //获取当前二维码id
            $quns = new Qun();
            $now_ewm_id = $quns->get_now_ewm($qun['id']);
            $ewm = "./" . config('shequn_file.dir') . "/" . $qun['key'] . "/".config('shequn_file.ewms')."/{$now_ewm_id}.jpg";
        }
        $ewm_im = imagecreatefromjpeg($ewm);
        //创建画布
        $canvas = imagecreatetruecolor(imagesx($bg_im), imagesy($bg_im));
        //合成二维码
        imagecopyresampled($canvas, $ewm_im, 50, 170, 0, 0, 360, 500, imagesx($ewm_im), imagesy($ewm_im));
        //合成背景
        imagecopyresampled($canvas, $bg_im, 0, 0, 0, 0, imagesx($bg_im), imagesy($bg_im), imagesx($bg_im), imagesy($bg_im));
        $imgfile = "/" . config('shequn_file.dir') . "/" . $qun['key'] . "/".config('shequn_file.uploads')."/" . enXcrypt($ewm_id) . '.jpg';
        imagejpeg($canvas, ".".$imgfile);
        imagedestroy($canvas);
        imagedestroy($ewm_im);
        imagedestroy($bg_im);
        return $imgfile;
    }
}