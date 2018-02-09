<?php

namespace app\common\model;

use think\Model;
use think\Db;
class LibraryArticle extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sg_library_article';

    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => 'rdsfjnifbfjnifbo.mysql.rds.aliyuncs.com',
//        // 数据库名
        'database'    => 'scrapy',
        // 数据库用户名
        'username'    => 'scrapy',
        // 数据库密码
        'password'    => '201671Zhuang',
        // 数据库连接端口
        'hostport'    => '',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => 'sc_',
    ];

    /**
     * 获取所有
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticle($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data=Db::connect($this->connection)->table($this->table)->where($where)->order('id desc')->field('content',true)->limit($limit, $rows)->select();
        array_walk($data,[$this,'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    public function formatter_date(&$value,$key)
    {
        if($value['addtime']){
            $value['addtime']=date("Y-m-d H:i:s",$value['addtime']);
        }
    }

    /**
     * 获取单篇文章
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        $key=self::get($id);
        return $key;
    }
}
