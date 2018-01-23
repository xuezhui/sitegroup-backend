<?php

namespace app\admin\controller;

use think\Config;
use think\Request;
use app\common\controller\Common;
use think\Validate;
use app\common\model\VoiceCdr as model;

class VoiceCdr extends Common
{
    /**
     * 显示资源列表
     *
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author guozhen
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $where = [];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data=(new model())->getAll($limits['limit'], $limits['rows'], $where);
        $data['timestart']=date('Y-m-d H:i:s',$data['timestart']);
        $data['timeend']=date('Y-m-d H:i:s',$data['timeend']);
        $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        return $this->resultArray('', '',$data);
    }
}