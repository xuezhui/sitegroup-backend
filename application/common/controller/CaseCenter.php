<?php

namespace app\common\controller;

use app\common\exception\ProcessException;
use think\Request;
use app\common\model\CaseCenter as this_model;
use think\Validate;

class CaseCenter extends CommonLogin
{
    /**
     * 初始化操作
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

    /**
     * 显示资源列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $keyword = $this->request->get('keyword');
        $content = $this->request->get('content');
        $industry_id = $this->request->get("industry_id");
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($keyword)) {
            $where["keyword"] = ["like", "%$keyword%"];
        }
        if (!empty($industry_id)) {
            $where['industry_id'] = $industry_id;
        }
        if (!empty($content)) {
            $where["content"] = $content;
        }
        $data = $this->model->getList($request["limit"], $request["rows"], $where);
        return $this->resultArray( $data);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        try {
            $rule = [
                ["title", "require", "请输入标题"],
                ["content", "require", "请输入内容"],
                ["industry_id", "require", "请选择行业分类"],
                ["industry_name", "require", "请选择行业分类"],
                ["keyword", "require", "请填写关键词"],
                ["summary", "require", "请输入核心解读"]
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->create($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray("添加成功");
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        $CaCenter = $this->model->get($id)->toArray();
        return $this->resultArray($CaCenter);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        try {
            $rule = [
                ["title", "require", "请输入标题"],
                ["content", "require", "请输入内容"],
                ["industry_id", "require", "请选择行业分类"],
                ["industry_name", "require", "请选择行业分类"],
                ["keyword", "require", "请填写关键词"],
                ["summary", "require", "请输入核心解读"]
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->save($data, ["id" => $id])) {
                Common::processException('修改失败');
            }
            return $this->resultArray('修改成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $CaCenter = $this->model->get($id);
        if (!$CaCenter->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }
}