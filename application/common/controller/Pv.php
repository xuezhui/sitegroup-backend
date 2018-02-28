<?php

namespace app\common\controller;

use app\common\model\CountData;
use app\common\model\BrowseRecord;
use think\Request;

class Pv extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        if (!empty($site_id)) {
            $where['site_id'] = $site_id;
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\Pv())->getAll($limits['limit'], $limits['rows'], $where));
    }
    
    /**
     * @return array
     * 统计关键词
     */
    public function countkeyword()
    {
        $param=$this->request->get();
        $starttime = 0;
        $stoptime = time();
        $user_info = $this->getSessionUserInfo();
        $where = [
            'node_id'=>$user_info["node_id"],
        ];
        //判断前台是否传递参数
        if(isset($param["time"])){
            list($start_time,$stop_time)=$param['time'];
            $starttime = (!empty(intval($start_time)))?strtotime($start_time):$starttime;
            $stoptime=(!empty(intval($stop_time)))?strtotime($stop_time):$stoptime;
        }
        $where["create_time"]=['between',[$starttime,$stoptime]];
        //判断前台有没有传递site——id参数
        if(!empty($param["site_id"])){
            $where['site_id']=$param['site_id'];
        }
        $browse=new BrowseRecord();
        $arr = $browse->field('keyword,count(id) as keyCount')->where($where)->group('keyword')->order("keyCount","desc")->select();
        $arrcount = $browse->where($where)->count();
        $temp=[];
        foreach ($arr as $k=>$v){
            //数据组织成百分比的形式
            $temp[]=["value"=>round($v['keyCount']/$arrcount*100,2),"name"=>$v['keyword']];
        }
        return $this->resultArray('','',$temp);
    }


}
