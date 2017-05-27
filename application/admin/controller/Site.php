<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Session;
use think\Validate;

/**
 * 站点 最小的节点相关操作
 * @author xingzhuang
 * 2017年5月17
 */
class Site extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @author xingzhuang
     */
    public function index()
    {
        $request = $this->getLimit();
        $site_name = $this->request->get('site_name');
        $where = [];
        if (!empty($site_name)) {
            $where["site_name"] = ["like", "%$site_name%"];
        }
        $user = (new Common())->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Site())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     * @author xingzhuang
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ['site_name','require','请填写网站名称'],
            ['menu', 'require', "请选择菜单"],
            ['template_id','require','请选择模板'],
            ['support_hotline','require','请填写电话号码'],
            ['domain_id','require','请选择域名'],
            ['domain','require','请选择域名'],
            ['site_type','require','请选择网站类型'],
            ['user_id',"require","请选择用户"],
            ["user_name","require","请选择用户名"],
            ["site_type_name","require","请填写网站类型名称"],
            ["keyword_ids","require","请填写关键字"],
            ["url","require","请输入url"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if(!$this->searchHttp($data["url"])){
            $data["url"]="http://".$data["url"];
        }
        if(!empty($data["link_id"])){
            $data["link_id"]=",".implode(",",$data["link_id"]).",";
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        $data["menu"]="," . implode(",",$data["menu"]) . ",";
        $data["keyword_ids"]="," . implode(",",$data["keyword_ids"]) . ",";

        if (!\app\admin\model\Site::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Site), $id);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ['site_name','require','请填写网站名称'],
            ['menu', 'require', "请选择菜单"],
            ['template_id','require','请选择模板'],
            ['support_hotline','require','请填写电话号码'],
            ['domain_id','require','请选择域名'],
            ['domain','require','请选择域名'],
            ['site_type','require','请选择网站类型'],
            ["site_type_name","require","请填写网站类型名称"],
            ["keyword_ids","require","请填写关键字"],
            ["url","require","请输入url"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        $user=$this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"]
        ];
        if(!empty($data["link_id"])){
            $data["link_id"]=",".implode(",",$data["link_id"]).",";
        }
        $data["menu"]="," . implode(",",$data["menu"]) . ",";
        $data["keyword_ids"]="," . implode(",",$data["keyword_ids"]) . ",";
        if (!(new \app\admin\model\Site)->save($data,$where)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源 模板暂时不支持删除操作
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {

    }


    /**
     * 传输模板文件到站点服务器
     * @access public
     */
    public function uploadTemplateFile($dest,$path)
    {
        $dest = $dest.'/index.php/filemanage/uploadFile';
        $this->sendFile(ROOT_PATH ."public/". $path, $dest, 'template');
    }

    /**
     * 修改为主站
     * @param $id
     * @return array
     */
    public function setMainSite($id)
    {
        $main_site=$this->request->post("main_site");
        if(empty($main_site)){
            return $this->resultArray('请选择是否是主站','failed');
        }
        $data=["main_site"=>$main_site];
        return $this->publicUpdate((new \app\admin\model\Site()),$data,$id);
    }

    /**
     * 修改ftp信息
     * @param $id
     * @return array
     */
    public function saveFtp($id)
    {
        $user=(new Common())->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"],
        ];
        $data=$this->request->put();
        if(!\app\admin\model\Site::where($where)->update($data)){
            return $this->resultArray('修改失败','failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 获取手机网站
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function mobileSite()
    {
        $user=(new Common())->getSessionUser();
        $where=[
            "is_mobile"=>20,
            "node_id"=>$user["user_node_id"],
            ];
        $data=(new \app\admin\model\Site)->where($where)->field("id,site_name as text")->select();
        return $this->resultArray('','',$data);
    }


    public function syncTemplate($id)
    {
        $user = $this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id" => $user["user_node_id"]
        ];
        $site=\app\admin\model\Site::where($where)->find();
        if(is_null($site)){
            return $this->resultArray('模板发送失败,无此记录!','failed');
        }
        $this->openObStart();
        printf([
            'status' => "success",
            'data' => '',
            'msg' => "正在发送模板,请等待.."
        ]);
        $template=\app\admin\model\Template::get($site->template_id);
        $this->uploadTemplateFile($site->url,$template->path);

    }
}