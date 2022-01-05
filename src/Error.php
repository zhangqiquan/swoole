<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Error.php    [ 2021/12/30 4:27 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);


namespace zhanshop;


class Error
{
    // http错误码
    public static $httpCode = 500;

    /**
     * 设置错误信息
     * @param int $code
     * @param string $msg
     */
    public static function setError(string $msg, int $code = 500, int $httpCode = 419){
        self::$httpCode = $httpCode;
        throw new \Exception($msg, $code);
    }

    /**
     * 系统异常处理
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     */
    public static function render(int $errno, string $errstr, string $errfile, string $errline){
        self::setError($errstr.'; errfile:'.$errfile.'; errline:'.$errline, 500, 500);
    }
}