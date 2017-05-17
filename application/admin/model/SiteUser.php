<?php

namespace app\admin\model;

use app\sysadmin\model\Node;
use think\Model;

class SiteUser extends Model
{
    //只读字段
    protected $readonly = ["node_id"];
    //初始化操作
    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        //写入事件
        SiteUser::event('before_write',function($siteuser){
            $siteuser->pwd=md5($siteuser->pwd.$siteuser->account);
            $siteuser->salt=chr(rand(97, 122)) . chr(rand(65, 90)) . chr(rand(97, 122)) . chr(rand(65, 90));
            $node=Node::get($siteuser->node_id);
            $siteuser->com_name=$node->com_name;
        });
    }

    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @author guozhen
     */
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time', true)->order('id', 'desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }

    /**
     * 格式化数据返回
     * @param $is_on
     * @return string
     */
    public function getIsOnAttr($is_on)
    {
        if ($is_on == 0) {
            return "禁用";
        }
        return "启用";
    }
}
