<?php
// +----------------------------------------------------------------------
// | XinAdmin [ A Full stack framework ]
// +----------------------------------------------------------------------
// | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小刘同学 <2302563948@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\model\admin;

use app\common\model\admin\AdminGroupModel as BaseAdminGroupModel;

class AdminGroupModel extends BaseAdminGroupModel
{

    public function getRulesAttr($value): array
    {
        if (!$value)return [];
        if($value == '*') {
            return (new AdminRuleModel())->where('status',1)->column('id');
        }else {
            return array_map('intval',explode(',',$value));
        }
    }

}