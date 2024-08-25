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
namespace app\common\model\setting;

use app\common\model\BaseModel;
use think\model\relation\HasMany;

/**
 * 设置分组模型
 */
class SettingGroupModel extends BaseModel
{
    protected $name = 'setting_group';

    public function setting(): HasMany
    {
        return $this->hasMany(SettingModel::class,'group_id');
    }
}