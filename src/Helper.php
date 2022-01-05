<?php
// +----------------------------------------------------------------------
// | zhanshop-swoole / Helper.php    [ 2021/12/30 4:28 下午 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2021 zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);


namespace zhanshop;


class Helper
{
    /**
     * 下划线转驼峰
     * 思路：step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符，step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     * @param string $uncamelized_words
     * @param string $separator
     * @return string
     */

    public static function camelize(string $uncamelized_words, string $separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }

    /**
     * 驼峰命名转下划线命名
     * 思路: 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     * @param string $camelCaps
     * @param string $separator
     * @return string
     */
    public static function uncamelize(string $camelCaps, string $separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    /**
     * 创建目录
     * @param string $path
     * @return bool
     */
    public static function mkdirs(string $path){
        if(!is_dir($path)){
            self::mkdirs(dirname($path));
            if(!mkdir($path, 0777)){
                return false;
            }
        }
        return true;
    }

    /**
     * 生成uuid
     * @param int $num
     * @return string
     */
    public static function uuid(int $num = 0){
        return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['REQUEST_TIME_FLOAT'].$num);
    }

    /**
     * 生成php文件头注释
     */
    public static function  headComment($class){
        $year = date('Y');
        $date = date('Y-m-d H:i:s');
        return "<?php
// +----------------------------------------------------------------------
// | {$class}【系统生成】   [ {$date} ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011~{$year} zhangqiquan All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangqiquan <768617998@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);\n\n";

    }
}
