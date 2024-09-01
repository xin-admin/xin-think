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

namespace app\admin\validate;

use think\Validate;

class MoneyLog extends Validate
{
    protected $rule = [
        'id' => 'require|number|max:10|min:1',
        'money' => 'require|integer',
        'remark' => 'require',
    ];

    protected $scene = [
        'add' => ['id', 'money', 'remark'],
    ];
}
