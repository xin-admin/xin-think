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

namespace app\admin\model\file;

use app\common\model\file\FileModel as BaseFileModel;

class FileModel extends BaseFileModel
{
    /**
     * 批量移动文件分组.
     */
    public function moveGroup(int $groupId, array $fileIds): bool
    {
        return (bool) static::update(['group_id' => $groupId], [['file_id', 'in', $fileIds]]);
    }

    /**
     * 添加文件库记录.
     * @param mixed $user_id
     * @return bool|false
     */
    public function add(array $data, int $fileType, $user_id, int $groupId = 0, int $channel = 10): bool
    {
        return $this->save([
            'group_id' => max($groupId, 0),
            'channel' => $channel,
            'storage' => $data['storage'],
            'domain' => $data['domain'],
            'file_name' => $data['file_name'],
            'file_path' => $data['file_path'],
            'file_size' => $data['file_size'],
            'file_ext' => $data['file_ext'],
            'file_type' => $fileType,
            'uploader_id' => $user_id,
        ]);
    }

    /**
     * 编辑记录.
     */
    public function edit(array $data): bool
    {
        return $this->allowField(['file_name', 'group_id'])->save($data) !== false;
    }
}
