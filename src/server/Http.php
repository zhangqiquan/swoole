<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Http.php    [ 2021/12/30 5:15 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace zhanshop\server;

use zhanshop\App;
use zhanshop\Helper;
use zhanshop\Request;


class Http
{
    /**
     * 实例化服务对象
     * @param string $ip
     * @param int $port
     * @param bool $ssl
     * @param bool $reuse_port
     * @return \Swoole\Http\Server
     */
    public function server(array &$config, string $processName){
        $serv = new \Swoole\Http\Server($config['server_ip'], $config['server_port'], $config['server_mode'], $config['server_socktype']);

        $this->config['pid_file'] = App::runtimePath().$processName.'.pid';
        $logFile = App::runtimePath().'swoole_log'.DIRECTORY_SEPARATOR.$processName.'.log';
        if(!file_exists($logFile)) Helper::mkdirs(dirname($logFile));
        $this->config['log_file'] = $logFile;

        unset($this->config['server_ip'], $this->config['server_port'], $this->config['server_mode'], $this->config['server_socktype']);

        /**
        $server->set([
        'ssl_cert_file' => $ssl_dir . '/ssl.crt',
        'ssl_key_file' => $ssl_dir . '/ssl.key',
        'open_http2_protocol' => true,
        ]);
         */

        $serv->set($this->config);

        $this->onEvent($serv);

        echo $config['server_ip'].':'.$config['server_port'].'启动成功'.PHP_EOL;

        return $serv;
    }

    /**
     * 触发事件
     * @param mixed $serv
     */
    public function onEvent(mixed $serv){
        $serv->on('Request', function ($request, $response) {
            $this->setServerData($request);
            $this->setRequestData($request);
            // 运行请求层逻辑
            try {
                $data = $this->runWithRequest($request);
                if(is_array($data)){
                    $response->header('Content-Type', 'application/json; charset=utf-8');
                    $data = json_encode($data, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
                }
                $response->status(200);
                return $response->end($data);
            }catch (\Throwable $e){
                $response->header('Content-Type', 'text/html; charset=utf-8');
                $response->status($e->getCode());
                return $response->end($e->getMessage().PHP_EOL.$e->getFile().' line:'.$e->getLine().PHP_EOL.$e->getTraceAsString());
            }
        });
    }

    public function runWithRequest($request){
        // 检查路由
        $requestData = App::route()->check($request->server['request_method']);
        $class = App::service()->get($requestData['controller']);
        $action = $requestData['action'];
        $data = $class->$action();
        return $data;
    }

    public function setRequestData(mixed $request){
        $_GET = $request->get ?? [];
        $_POST = $request->post ?? [];
        $_REQUEST = array_merge($_GET, $_POST);
        $_FILES = $request->files ?? [];
    }


    public function setServerData(mixed $request){
        foreach($request->header as $k => $v){
            $_SERVER['HTTP_'.strtoupper($k)] = $v;
        }
        foreach($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
        $_SERVER['PATH_INFO'] = ltrim($_SERVER['PATH_INFO'], '/');
    }

}