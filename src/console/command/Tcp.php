<?php

namespace zhanshop\console\command;

use zhanshop\console\Command;
use zhanshop\console\Input;
use zhanshop\console\Output;

class Tcp extends Command
{
    public function configure()
    {
        $this->setTitle('启动tcp服务')->setDescription('使用该命令可以创建一个tcp服务');
    }

    public function execute(Input $input, Output $output){

    }
}