<?php

namespace app\common\controller;

use app\common\model\WangyiArticle;

class Wangyi extends CommonLogin
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new WangyiArticle();
    }

    /**
     * 获取所有网易爬虫文章
     *
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $type_id = $this->request->get('type_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($type_id)) {
            $where["type_id"] = $type_id;
        }
        $data = $this->model->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
    }


    /**
     * 获取某个文章
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray($this->model->getOne($id)->toArray());
    }

    /**
     * 获取所有分类
     * @return array
     */
    public function getTypes()
    {
        return $this->resultArray($this->model->allTypes());
    }
}