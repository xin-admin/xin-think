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
namespace app\common\model\dict;

use app\common\model\BaseModel;
use think\model\relation\HasMany;

/**
 * 字典模型
 */
class DictModel extends BaseModel
{

    protected $name = 'dict';


    public function dictItems(): HasMany
    {
        return $this->hasMany(DictItemModel::class,'dict_id');
    }
}