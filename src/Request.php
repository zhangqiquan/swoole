<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Request.php    [ 2021/12/30 4:34 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);


namespace zhanshop;


class Request
{
    /**
     * 请求类型
     * @var string
     */
    protected $varMethod = '_method';

    /**
     * 获取request变量
     * @access public
     * @param  string|array $name 数据名称
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function request($name = '', $default = null, $filter = 'addslashes')
    {
        $_REQUEST = array_merge($_REQUEST, $this->getInputData(file_get_contents('php://input')));
        if ($name == '') {
            $data = $this->filterData($_REQUEST, $filter);
            return $data;
        }
        $data = $_REQUEST[$name] ?? $default;
        return $this->filterData($data, $filter);
    }

    /**
     * 获取上传的文件信息
     * @access public
     * @param  string $name 名称
     * @return null|array
     */
    public function file(string $name = '')
    {
        if (!empty($_FILES)) {
            if (strpos($name, '.')) {
                [$name, $sub] = explode('.', $name);
            }

            if ('' === $name) {
                // 获取全部文件
                return $_FILES;
            } elseif (isset($sub) && isset($array[$name][$sub])) {
                return $_FILES[$name][$sub];
            } elseif (isset($array[$name])) {
                return $_FILES[$name];
            }
        }
    }

    /**
     * 设置或者获取当前的Header
     * @access public
     * @param  string $name header名称
     * @param  string $default 默认值
     * @return string|array
     */
    public function header(string $name = '', string $default = null, string $filter = 'addslashes')
    {
        $header = [];
        $server = $_SERVER;
        foreach ($server as $key => $val) {
            if (0 === strpos($key, 'HTTP_')) {
                $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                $header[$key] = $val;
            }
        }
        if (isset($server['CONTENT_TYPE'])) {
            $header['content-type'] = $server['CONTENT_TYPE'];
        }
        if (isset($server['CONTENT_LENGTH'])) {
            $header['content-length'] = $server['CONTENT_LENGTH'];
        }
        $header = array_change_key_case($header);


        if ('' === $name) {
            return $this->filterData($header, $filter);
        }

        $data = $_REQUEST[$name] ?? $default;
        return $this->filterData($data, $filter);
    }


    /**
     * 获取POST参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function post($name = '', $default = null, $filter = 'addslashes')
    {
        if ($name == '') {
            $data = $this->filterData($_POST, $filter);
            return $data;
        }
        $data = $_POST[$name] ?? $default;
        return $this->filterData($data, $filter);
    }

    /**
     * 当前的请求类型
     * @access public
     * @param  bool $origin 是否获取原始请求类型
     * @return string
     */
    public function method(bool $origin = false): string
    {
        $method = 'GET';
        if ($origin) {
            // 获取原始请求类型
            $method = $_SERVER['request_method'] ?: 'GET';
        } else{
            $varMethod = $this->post($this->varMethod);
            if ($varMethod) {
                $method = strtolower($varMethod);
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $method    = strtoupper($method);
                } else {
                    $method = 'POST';
                }
                unset($_POST[$this->varMethod]);
            } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }

        return $method;
    }


    /**
     * 获取输入参数
     * @param $content
     * @return array
     */
    protected function getInputData($content): array
    {
        $contentType = $this->contentType();
        if ('application/x-www-form-urlencoded' == $contentType) {
            parse_str($content, $data);
            return $data;
        } elseif (false !== strpos($contentType, 'json')) {
            return (array) json_decode($content, true);
        }

        return [];
    }


    /**
     * 字段过滤
     * @param  $data
     * @param  $filter
     */
    protected function filterData(&$data, $filter){
        if(is_array($data)){
            foreach($data as $k => $v){
                $this->filterData($data[$k], $filter);
            }
        }else{
            $data = $filter($data);
        }
    }

    /**
     * 获取当前请求的时间
     * @access public
     * @param  bool $float 是否使用浮点类型
     * @return integer|float
     */
    public function time(bool $float = false)
    {
        return $float ? $_SERVER['request_time'] : $_SERVER['request_time_float'];
    }

    /**
     * 当前是否Pjax请求
     * @access public
     * @param  bool $pjax true 获取原始pjax请求
     * @return bool
     */
    public function isPjax(bool $pjax = false): bool
    {
        $result = !empty($_SERVER['http_x_pjax']) ? true : false;

        if (true === $pjax) {
            return $result;
        }

        return $this->param($this->varPjax) ? true : $result;
    }


    /**
     * 获取客户端IP地址
     * @access public
     * @return string
     */
    public function ip(): string
    {
        $realIP = $_SERVER['remote_addr'] ?? 'unknown';
        return $realIP;
    }

    /**
     * 当前请求 REMOTE_PORT
     * @access public
     * @return int
     */
    public function remotePort(): int
    {
        return (int) $_SERVER['remote_port'] ?? 0;
    }

}