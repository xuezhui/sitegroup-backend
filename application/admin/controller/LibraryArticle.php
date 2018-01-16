<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use app\admin\model\LibraryArticle as Library;
use think\Validate;

class LibraryArticle extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Library();
    }

    /**
     * 获取所有爬虫文章
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title= $this->request->get('title');
        $type_id= $this->request->get('type_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if(!empty($type_id)){
            $where["type_id"]=$type_id;
        }
        $data = $this->conn->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }




    /**
     * 获取某个文章
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }


}
