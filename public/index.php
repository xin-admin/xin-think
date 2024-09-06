<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;


//$server = $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
//if(!$server) {
//    $rootPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
//    // 是否安装
//    if (!is_file($rootPath . 'install.lock') && is_file($rootPath . 'install' . DIRECTORY_SEPARATOR . 'index.html')) {
//        header("location:" . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR);
//        exit();
//    }
//}


require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();;

$response->send();

$http->end($response);
