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

class Setting extends Validate
{
    protected $rule = [
        'id' => 'require|max:10|number',
        'title' => 'require|max:10',
        'key' => 'require|max:10',
        'describe' => 'require',
        'values' => 'require',
        'group_id' => 'require',
    ];

    protected $scene = [
        'addGroup' => ['title', 'key'],
        'editGroup' => ['id', 'title', 'key'],
        'add' => ['title', 'key', 'group_id', 'type'],
        'list' => ['group_id'],
        'edit' => ['id', 'title', 'key', 'group_id', 'type'],
    ];
}
