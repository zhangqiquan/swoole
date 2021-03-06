<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Query.php    [ 2021/12/30 5:10 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace zhanshop\database;

use kernel\App;
use kernel\Error;
use think\db\Query as Base;

class Query extends Base
{
    /**
     * 获取表上的统计量
     * @return int|mixed
     */
    public function getTableCount(){
        $data = $this->getConnection()->query('SHOW TABLE STATUS where name="'.$this->name.'"');
        if(isset($data[0]['Rows'])) return $data[0]['Rows'];
        return 0;
    }

    /**
     * finder列表 200万以内的数据使用count获取总数;超过200万的数据使用表上的统计量
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function finder(int $page = 1, int $limit = 20){

        $page        = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : $page;
        $limit       = isset($_REQUEST['limit']) && $_REQUEST['limit'] ? $_REQUEST['limit'] : $limit;
        $currentPage = $page;
        $page        = ($page - 1) * $limit;

        return [
            'total' => $this->count(),
            'current_page' => $currentPage,
            'data' => $this->limit($page, $limit)->select()->toArray()
        ];
    }

    /**
     * 删除
     * @param null $data
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function delete($data = null): int
    {
        // 软删除字段被定义，并且软删除参数存在
        $deleteField = $this->getOptions('soft_delete');
        $this->removeOption('soft_delete');
        if($deleteField){
            $deleteField = $deleteField[0];
            $array = explode('.', $deleteField);
            $deleteField = array_pop($array);
            if($deleteField) return parent::update([$deleteField => time()]); // 走更新

        }
        return parent::delete($data); // TODO: Change the autogenerated stub
    }

    protected function throwNotFound(): void
    {
        App::log()->error($this->getLastSql()); // 记录一条错误日志
        Error::setError("您所访问的数据不存在或已被删除", 404);
    }
}
