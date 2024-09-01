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

namespace app\admin\validate\system;

use think\Validate;

class Dict extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|max:10',
        'code' => 'require|max:10',
    ];

    protected $scene = [
        'edit' => ['id', 'name', 'code'],
        'add' => ['name', 'code'],
    ];
}
