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

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 用户分组模型.
 */
class UserGroupModel extends BaseModel
{
    protected $name = 'user_group';

    public function getRulesAttr($value): array
    {
        if ($value == '*') {
            return (new UserRuleModel())->where('status', 1)->column('id');
        }
        return array_map('intval', explode(',', $value));
    }
}
