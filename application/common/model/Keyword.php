<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;

use app\common\controller\Common;

use think\Model;

class Keyword extends Model
{
    //只读字段
    protected $readonly=["node_id"];

    /**
     * 根据tag获取数据
     * @param int $id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getKeyword($id=0)
    {
        $where=[];
        $where["parent_id"]=$id;
        $user=(new Common)->getSessionUserInfo();
        $where["node_id"]=$user["node_id"];
        $data=$this->where($where)->field("id,name,tag")->select();
        return $data;
    }

    /**
     * 获取A类关键词
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function keyword(){
        $where=[];
        $where['tag']="A";
        $user=(new Common)->getSessionUserInfo();
        $where["node_id"]=$user["node_id"];
        $data = $this->where($where)->field('id,name as text')->select();
        return $data;

    }
}