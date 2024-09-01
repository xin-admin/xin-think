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

namespace app\common\enum\ApiEnum;

enum ShowType: int
{
    // 无状态响应
    case SILENT = 0;

    // 警告响应
    case WARN_MESSAGE = 1;

    // 失败响应
    case ERROR_MESSAGE = 2;

    // 通知响应
    case NOTIFICATION = 3;

    // 待处理响应
    case REDIRECT = 9;
}
