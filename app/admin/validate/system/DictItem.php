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

class DictItem extends Validate
{
    protected $rule = [
        'id' => 'require',
        'label' => 'require|max:20',
        'value' => 'require|max:20',
        'weigh' => 'require',
        'status' => 'require',
    ];

    protected $scene = [
        'edit' => ['id', 'label', 'value', 'status'],
        'add' => ['label', 'value', 'status'],
    ];
}
