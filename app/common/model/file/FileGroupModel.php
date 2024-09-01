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

namespace app\common\model\file;

use app\common\model\BaseModel;

/**
 * 文件分组模型.
 */
class FileGroupModel extends BaseModel
{
    protected $name = 'file_group';

    protected $pk = 'group_id';

    /**
     * 分组详情.
     */
    public static function detail(array|int $where): null|array|static
    {
        return self::get($where);
    }
}
