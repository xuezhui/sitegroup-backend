<?php
// +----------------------------------------------------------------------
// | Description: 用户
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Config;
use think\Db;
use think\Model;
use app\common\controller\Common;
use think\Session;

class User extends Model
{
    /**
     * 用户验证
     * @param $usrname
     * @param $pwd
     * @return array
     * @auther guozhen
     */
    public function checkUser($username, $pwd)
    {
        $common = new Common();
        $user_info = $this::where(["user_name" => $username])->find();
        if(empty($user_info)){
            return $common->resultArray("用户名错误", "failed");
        }
        $user_info_arr = $user_info->toArray();
        if (md5($pwd . $username) != $user_info_arr["pwd"]) {
            return $common->resultArray("用户名或密码错误", "failed");
        }
        unset($user_info["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_info["remember"] = md5($user_info["id"] . $user_info["salt"] . $private);
        return $common->resultArray("登录成功!!", "", $user_info);
    }

    /**
     * 修改密码
     * @param $oldPwd
     * @param $newPwd
     * @return array
     */
    public function changePwd($oldPwd,$newPwd)
    {
        $common=new Common();
        $user_id=Session::get("user_id");
        $user_info=$this::get($user_id);
        if(empty($user_info)){
            return $common->resultArray('登录超时,请重新登录','failed');
        }
        if($user_info->pwd!=md5($oldPwd.$user_info->user_name)){
            return $common->resultArray('原密码错误','failed');
        }
        //原密码和新密码相同
        if($oldPwd == $newPwd){
            return $common->resultArray('原密码和新密码不能相同','failed');
        }
        //新密码长度
        if(strlen($newPwd)<6){
            return $common->resultArray('新密码不能小于6位','failed');
        }
        if($this->where(["id"=>$user_id])->update(["pwd"=>md5($newPwd.$user_info->user_name)])){
            return $common->resultArray('密码修改失败','failed');
        }
        return $common->resultArra('密码修改成功');
    }
}