<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');
if (!defined("DS")) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined("WT_ROOT")) {
    define('WT_ROOT', realpath(dirname(__FILE__)));
}
SeasLog::setBasePath(WT_ROOT . DS . ".." . DS . "logs");
// img_upload.php
// 'one_image' refers to your file input name attribute
if (empty($_FILES['one_image'])) {
    echo json_encode(['error'=>'No files found for upload.']);
    // or you can throw an exception
    return; // terminate
}

// get the files posted
$one_image = $_FILES['one_image'];

// get user id posted
$project = empty($_POST['project']) ? '' : $_POST['project'];

// get user name posted
$version = empty($_POST['version']) ? '' : $_POST['version'];

SeasLog::debug("project--{$project}");
SeasLog::debug("version--{$version}");

// a flag to see if everything is ok
$success = null;



// get file names
$filenames = $one_image['name'];

$ext = explode('.', basename($filenames));
$new_name = md5(uniqid()) . "." . array_pop($ext);


/**
 * 创建目录
 */
$app_root = WT_ROOT. DS . ".." . DS . "data" . DS . $project. DS . $version. DS;
$app_url = "../data/{$project}/{$version}/" ;
$target_path = $app_root . $new_name;
$target_url = $app_url . $new_name;
if(move_uploaded_file($one_image['tmp_name'], $target_path)) {
    $success = true;
} else {
    $success = false;
}

// check and process based on successful status
if ($success === true) {
    // call the function to save all data to database
    // code for the following function `save_data` is not
    // mentioned in this example
    //save_data($project, $version, $paths);

    // store a successful response (default at least an empty array). You
    // could return any additional response info you need to the plugin for
    // advanced implementations.
    $output = [
        'code'=>'ok',
        'img_url' => $target_url,
        'img_id' => $new_name
    ];
    // for example you can get the list of files uploaded this way
    // $output = ['uploaded' => $paths];
} else{
    $output = ['error'=>'Error while uploading one_image. Contact the system administrator'];
    // delete any uploaded files
}

// return a json encoded response for plugin to process successfully
echo json_encode($output);