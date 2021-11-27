<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set("Asia/ShangHai");
if (!defined("DS")) {
    define("DS", DIRECTORY_SEPARATOR);
}
if (!defined("UP_ROOT")) {
    define("UP_ROOT", realpath(dirname(__FILE__)));
}

define("URL_ROOT", "http://127.0.0.1:80/nsh_app_update");

/**
 * 目录结构
 * nsh_app_update/
 * ----apks/             更新包文件目录
 * --------1xxx.xx.apk
 * --------2xxx.xx.apk
 * ----stat/             统计数据
 * --------v1.0/
 * ------------sn1_v_ip.json
 * ------------sn2_v_ip.json
 * --------v1.5/
 * ------------sn1_v_ip.json
 * ------------sn2_v_ip.json
 * ----meta-v1.0.json    v1.0版本信息
 * ----meta-v1.5.json    v1.5版本信息
 * ----probe.php         入口程序
 */

$g_stat_root = UP_ROOT . DS . "stat";
//判断form数据是否为POST而来，判断数据提交方式
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // 展示统计
    $a_v1_0 = array();
    $a_v1_5 = array();
    if (!file_exists($g_stat_root) || !is_dir($g_stat_root)) {
        @mkdir($g_stat_root);
    }
    $g_stat_v10 = $g_stat_root . DS . "v1.0";
    $g_stat_v15 = $g_stat_root . DS . "v1.5";
    if (!file_exists($g_stat_v10) || !is_dir($g_stat_v10)) {
        @mkdir($g_stat_v10);
    }
    if (!file_exists($g_stat_v15) || !is_dir($g_stat_v15)) {
        @mkdir($g_stat_v15);
    }

    //1、首先先读取文件夹
    $a_temp = scandir($g_stat_v10);
    //遍历文件夹
    foreach ($a_temp as $v) {
        $aa = $g_stat_v10 . DS . $v;
        if (is_dir($aa)) {
            //如果是文件夹则执行
            continue;
        } else {
            //echo $aa , "<br/>";
            if (str_ends_with($aa, ".json")) {
                $a_one = explode("_", $aa);
                $sn = $a_one[0];
                $ver = $a_one[1];
                $ip = $a_one[2];
                $new_info = array("sn" => $sn, "ver" => $ver, "ip" => $ip);
                if (!isset($a_v1_0[$sn])) {
                    $a_v1_0[$sn] = array();
                }
                $a_v1_0[$sn][] = $new_info;
            }
        }
    }
    $a_temp = scandir($g_stat_v15);
    //遍历文件夹
    foreach ($a_temp as $v) {
        $aa = $g_stat_v15 . DS . $v;
        if (is_dir($aa)) {
            //如果是文件夹则执行
            continue;
        } else {
            //echo $aa , "<br/>";
            if (str_ends_with($aa, ".json")) {
                $a_one = explode("_", $aa);
                $sn = $a_one[0];
                $ver = $a_one[1];
                $ip = $a_one[2];
                $new_info = array("sn" => $sn, "ver" => $ver, "ip" => $ip);
                if (!isset($a_v1_5[$sn])) {
                    $a_v1_5[$sn] = array();
                }
                $a_v1_5[$sn][] = $new_info;

            }
        }
    }
    echo '<html lang="en"><head><meta charset="utf-8"/></head><body>';
    echo '<h1>设备和版本统计</h1>';
    echo '<table border="1">';
    echo '<tr><th>V1.0</th><th>V1.5</th></tr>';
    echo '<tr><td>';

    echo '<table border="1">';
    echo '<tr><th>SN</th><th>VERSION</th><th>IP</th></tr>';
    foreach ($a_v1_0 as $sn => $a_rows) {
        foreach ($a_rows as $a_info) {
            echo '<tr>';
            echo '<td>';
            echo $a_info['sn'];
            echo '</td>';
            echo '<td>';
            echo $a_info['ver'];
            echo '</td>';
            echo '<td>';
            echo $a_info['ip'];
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';

    echo '</td><td>';

    echo '<table border="1">';
    echo '<tr><th>SN</th><th>VERSION</th><th>IP</th></tr>';
    foreach ($a_v1_5 as $sn => $a_rows) {
        foreach ($a_rows as $a_info) {
            echo '<tr>';
            echo '<td>';
            echo $a_info['sn'];
            echo '</td>';
            echo '<td>';
            echo $a_info['ver'];
            echo '</td>';
            echo '<td>';
            echo $a_info['ip'];
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';

    echo '</td></tr>';
    echo '</table>';
    echo '</body></html>';
    //$a_all_final = array_unique($a_all);

} else {
    $a_result = array();
    $a_result["code"] = 500;
    $a_result["msg"] = "no_sn";
    $a_result["data"] = "null";
    //需要restful风格的提交，不能是 enctype="multipart/form-data"
    $str_param = file_get_contents("php://input");
    $a_param = json_decode($str_param, true);

    /**
     * {
     * "sn":"8CFCA0F31F60",//设备SN号
     * "versionName":"V1.1.94",//设备当前应用版本名
     * "versionCode":94//设备当前应用版本号
     * "deviceVersion":"1.5"
     * }
     */
    if (isset($a_param["sn"])) {
        $sn = $a_param["sn"];
        $versionName = $a_param["versionName"];
        $versionCode = $a_param["versionCode"];
        $deviceVersion = $a_param["deviceVersion"];
        $path_meta = "";
        $deviceVersion = strtolower($deviceVersion);
        if ($deviceVersion != "v1.0" && $deviceVersion != "v1.5") {
            $a_result["code"] = 501;
            $a_result["msg"] = "unknown_device_version";
            echo json_encode($a_result);
            return;
        }
        //先保存到本地
        $ip = $_SERVER['REMOTE_ADDR'];
        $path_save = UP_ROOT . DS . "stat" .DS. $deviceVersion;
        $versionCode = strtolower($versionCode);
        $sn = strtoupper($sn);
        $file_name0 = $sn . "_" . $versionCode . "_" . $ip . ".json";
        $path_save = $path_save . DS . $file_name0;
        file_put_contents($path_save, $str_param);

        //读取新信息返回
        if ($deviceVersion == "v1.0") {
            $path_meta = UP_ROOT . DS . "meta-v1.0.json";
        } elseif ($deviceVersion == "v1.5") {
            $path_meta = UP_ROOT . DS . "meta-v1.5.json";
        }

        $a_meta = json_decode(file_get_contents($path_meta), true);
        $versionName2 = $a_meta["versionName"];
        $versionCode2 = $a_meta["versionCode"];
        $fileName = $a_meta["fileName"];

        $a_result["code"] = 200;
        $a_result["msg"] = "last";
        $a_data = array();
        $a_data["versionName"] = $versionName2;
        $a_data["versionCode"] = $versionCode2;
        $a_data["fileName"] = $fileName;
        $a_data["url"] = URL_ROOT . "/apks/" . $fileName;
        $a_data["isSilence"] = true;
        $a_data["time"] = time();
        $a_result["msg"] = $a_data;
    }
    echo json_encode($a_result);
    return;
}




/***
 * {
 * "code": 200,
 * "msg": null,
 * "data": {
 * "versionName": "V1.1.94",
 * "versionCode": 94,
 * "fileName": "nsh_v1.1.94.apk",
 * "url": "http://xxx.xxx.xxx:8080/update/nsh_v1.1.94.apk",
 * "isSilence": true,
 * "time": 1637982736632
 * }
 * }*/
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