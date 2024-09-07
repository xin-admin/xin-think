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
namespace app\admin\model;

use app\admin\model\admin\AdminModel;
use app\common\model\BaseModel;
use think\model\relation\BelongsTo;

class MonitorModel extends BaseModel
{
    protected $name = "monitor";

    protected $pk = "id";

        /**
     * 关联会员记录表
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminModel::class,'user_id','id');
    }

}
