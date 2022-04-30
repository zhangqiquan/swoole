<?php

namespace zhanshop\console\command;

use zhanshop\console\Input;
use zhanshop\console\Output;

class Websocket
{
    public function configure()
    {
        $this->setTitle('启动websocket网络服务')->setDescription('使用该命令可以创建一个websocket服务');
    }

    public function execute(Input $input, Output $output){

    }
}