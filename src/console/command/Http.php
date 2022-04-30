<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Http.php    [ 2021/12/30 4:18 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
namespace zhanshop\console\command;

use zhanshop\console\Command;
use zhanshop\console\Input;
use zhanshop\console\Output;

class Http extends Command
{
    public function configure()
    {
        $this->setTitle('启动http服务')->setDescription('使用该命令可以创建一个http服务');
    }

    public function execute(Input $input, Output $output){

    }
}