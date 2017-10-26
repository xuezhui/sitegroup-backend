<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;

use think\Model;

class Article extends Model
{
    //只读字段
    protected $readonly = ["node_id"];

    /**
     * 初始化函数
     * @author guozhen
     */
    public static function init()
    {
        parent::init();
        // 文章阅读数量随机生成 添加图片缩略图
        Article::event("before_insert", function ($article) {
            if (isset($article->content)) {
                //如果已经上传了缩略图的话
                $url = "https://lexiaoyi.oss-cn-beijing.aliyuncs.com/";
                // 匹配阿里云网址
                if (!empty($article->thumbnails) && (strpos($article->thumbnails, $url) !== false)) {
                    //获取图片信息
                    $img_info=pathinfo($article->thumbnails);
                    //拼接缩略图名称
                    $article->thumbnails_name = md5(uniqid(rand(), true)) .".".$img_info["extension"];
                }
            }
            //如果阅读数量是空的话
            if (empty($article->readcount)) {
                $article->readcount = rand(100, 10000);
            }
        });
        //修改操作
        Article::event("before_update", function ($article) {
            //如果阅读数量是空的话
            if (empty($article->readcount)) {
                $article->readcount = rand(100, 10000);
            }
        });
    }

    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getArticle($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('content,summary,update_time,readcount',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getArticletdk($limit, $rows, $w = '',$wheresite='',$wheretype_id='')
    {
        $count = $this->where('articletype_id', 'in', $wheretype_id)->where($w)->whereOr($wheresite)->count();
        $articledata = $this->limit($limit, $rows)->where('articletype_id', 'in', $wheretype_id)->where($w)->whereOr($wheresite)->field('id,title,create_time')->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $articledata
        ];
    }


}