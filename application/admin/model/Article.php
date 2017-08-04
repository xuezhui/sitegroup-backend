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
        parent::init(); // TODO: Change the autogenerated stub
        // 文章阅读数量随机生成
        Article::event("before_write", function ($article) {
            $rule = '/<img[\s\S]*?src\s*=\s*[\"|\'](.*?)[\"|\'][\s\S]*?>/';
            if (isset($article->content)) {
                preg_match($rule, $article->content, $matches);
                if (!empty($matches)) {
                    $article->thumbnails = $matches[0];
                }
            }
            $article->readcount = rand(100, 10000);
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
}