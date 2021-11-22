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
