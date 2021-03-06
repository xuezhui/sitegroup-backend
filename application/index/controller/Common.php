<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\index\controller;


use app\index\model\SystemConfig;
use think\Controller;
use think\Session;

class Common extends Controller
{

    /**
     * 本地测试开启下 允许跨域ajax 获取数据
     */
    function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin:" . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Credentials: true ");
    }

    /**
     * 获取配置信息
     */
    function getConfigInfo()
    {
        $systemConfig = cache('DB_CONFIG_DATA');
        if (!$systemConfig) {
            //获取所有系统配置
            $systemConfig = (new SystemConfig())->getDataList();
            cache('DB_CONFIG_DATA', null);
            cache('DB_CONFIG_DATA', $systemConfig, 36000); //缓存配置
        }
        Session::set('name', 'dede');
        $session_id = $_COOKIE["PHPSESSID"];
        $systemConfig['session_id'] = $session_id;
        return $this->resultArray(['data' => $systemConfig]);
    }

    /**
     * 获取 验证码测试
     * @access public
     */
    public function getCaptcha()
    {
        //captcha_check()
        print_r(Session::get('name'));
    }


    /**
     * 返回对象
     * @param $array 响应数据
     * @return array
     */
    function resultArray($array)
    {
        if (isset($array['data'])) {
            $array['error'] = '';
            $code = 200;
        } elseif (isset($array['error'])) {
            $code = 400;
            $array['data'] = '';
        }
        return [
            'code' => $code,
            'data' => $array['data'],
            'error' => $array['error']
        ];
    }
}
 