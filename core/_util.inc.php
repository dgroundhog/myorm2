<?php

/*生成唯一标志
*标准的UUID格式为：xxxxxxxx-xxxx-xxxx-xxxxxx-xxxxxxxxxx(8-4-4-4-12)
*/
function uuid()
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 0, 8) . '-'
        . substr($chars, 8, 4) . '-'
        . substr($chars, 12, 4) . '-'
        . substr($chars, 16, 4) . '-'
        . substr($chars, 20, 12);
    return $uuid;
}

/**
 * 文件夹复制
 * 用法：
 *  dir_copy("feiy","feiy2",1):拷贝feiy下的文件到 feiy2,包括子目录
 *  dir_copy("feiy","feiy2",0):拷贝feiy下的文件到 feiy2,不包括子目录
 *参数说明：
 * $source:源目录名
 * $destination:目的目录名
 * $child:复制时，是不是包含的子目录
 *
 * @param $source
 * @param $destination
 * @param int $child
 * @return int
 */
function dir_copy($source, $destination, $child = 1)
{
    if (!is_dir($source)) {
        SeasLog::error("Error:the $source is not a direction!");
        return false;
    }


    if (!is_dir($destination)) {
        mkdir($destination, 0777);
    }

    $handle = dir($source);
    while ($entry = $handle->read()) {
        if (($entry != ".") && ($entry != "..")) {
            if (is_dir($source . "/" . $entry)) {
                if ($child)
                    dir_copy($source . "/" . $entry, $destination . "/" . $entry, $child);
            } else {
                copy($source . "/" . $entry, $destination . "/" . $entry);
            }
        }
    }

    return true;
}

/**
 * 创建多级目录
 * @param $path
 * @param $mode
 * @return void
 */
function dir_create($path, $mode = 0777){
    SeasLog::info('准备创建目录--'.$path);
    if(is_dir($path)){
        SeasLog::info('无法创建,已经是目录了');
        return ;
    }else{
        if(mkdir($path, $mode, true)) {
            SeasLog::info('创建成功');
        }else{
            SeasLog::info('创建失败');
        }
    }
}


/**
 * 独立函数
 */
function str_starts_with($haystack, $needle)
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function str_ends_with($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

/**
 * 判断是否合法的名字，字母开头，字母数字组合，2-32个字符
 * @param $name_to_be
 * @return false|int
 */
function is_ok_project_name($name_to_be)
{
    $name_to_be = strtolower($name_to_be);
    return preg_match("/^[a-z][0-9a-z]{1,32}$/i", $name_to_be);
}

/**
 * 判断是否合法的包名
 * aa.bb.cc
 * aa\bv\cc
 * @param $name_to_be
 * @return false|int
 */
function is_ok_app_package($name_to_be)
{
    $name_to_be = strtolower($name_to_be);
    return preg_match("/^[a-zA-Z]+[0-9a-zA-Z_]*(\.[a-zA-Z]+[0-9a-zA-Z_]*)*(\\[a-zA-Z]+[0-9a-zA-Z_]*)*$/i", $name_to_be);
}


/**
 * 下划线转驼峰
 * 思路:
 * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 * @param $uncamelized_words
 * @param $separator
 * @return string
 */
function str_camelize($uncamelized_words, $separator = '_'){
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);

}
/**
 * 驼峰命名转下划线命名
 * 思路:
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * @param $camelCaps
 * @param $separator
 * @return string
 */
function str_uncamelize($camelCaps, $separator = '_'){
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}


/**
 * 判断字符串
 * @param $haystack
 * @param $needle
 * @return bool
 */
function str_start_with($haystack, $needle)
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
