<?php
/*
 *  +----------------------------------------------------------------------
 *  | XinAdmin [ A Full stack framework ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: 小刘同学 <2302563948@qq.com>
 *  +----------------------------------------------------------------------
 */
// | XinAdmin 设置
// +----------------------------------------------------------------------
return [
    // token 驱动设置
    'token' => [
        // 默认驱动方式
        'default' => 'mysql',
        // 加密key
        'key' => 'C5LziFeF2lNIOn4cMgZr17x80vHWAjwD',
        // 加密方式
        'algo' => 'ripemd160',
        // 驱动
        'stores' => [
            'mysql' => [
                'type' => 'Mysql',
                // 留空表示使用默认的 Mysql 数据库，也可以填写其他数据库连接配置的`name`
                'name' => '',
                // 存储token的表名
                'table' => 'token',
                // 默认 token 有效时间
                'expire' => 2592000,
            ],
            'redis' => [
                'type' => 'Redis',
                'host' => '127.0.0.1',
                'port' => 6379,
                'password' => '',
                'select' => false,
                'timeout' => 0,
                'expire' => 0,
                'persistent' => false,
                'userprefix' => 'xin:',
            ],
        ],
    ],
    // 文件储存设置
    'storage' => [
        // 默认储存方式
        'default' => 'local',
        // 引擎
        'engine' => [
            // 本地储存
            'local' => [
            ],
        ],
    ],
];
