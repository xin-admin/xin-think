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
namespace app\common\model\file;

use app\common\enum\FileType as FileTypeEnum;
use app\common\model\BaseModel;
use think\model\relation\BelongsTo;

/**
 * 文件模型
 */
class FileModel extends BaseModel
{
    protected $name = 'file';

    // 定义主键
    protected $pk = 'file_id';

    // 追加的字段
    protected $append = [
        'preview_url',     // 图片预览url
        'external_url'     // 文件外链url (用于视频文件)
    ];

    public function saveFile(array $data, int $user_id): bool
    {
        try {
            $file = $this->where('file_name', $data['file_name'])->find();
            if ($file) {
                return true;
            }

            $fileGroup = new FileGroupModel();
            if (!isset($data['group_id'])) {
                $group = $fileGroup->where('user_id', $user_id)->where('pid', 0)->find();
                if (!$group) {
                    $fileGroup->save([
                        'pid' => 0,
                        'user_id' => $user_id,
                        'name' => 'root'
                    ]);
                    $data['group_id'] = $fileGroup->id;
                } else {
                    $data['group_id'] = $group->id;
                }
            }

            $this->save($data);

            return true;
        } catch (\Exception $e) {
            $this->setErrorMsg($e->getMessage());
            return false;
        }

    }

    /**
     * 关联模型：文件库分组
     * @return BelongsTo
     */
    public function fileGroup(): BelongsTo
    {
        return $this->belongsTo('FileGroup', 'group_id');
    }

    /**
     * 生成预览url (preview_url)
     * @param $value
     * @param $data
     * @return string
     */
    public function getPreviewUrlAttr($value, $data): string
    {
        // 图片的预览图直接使用外链
        if ($data['file_type'] == '10') {
            return $this->getExternalUrlAttr($value, $data);
        }
        // 生成默认的预览图
        $previewPath = FileTypeEnum::data()[$data['file_type']]['preview_path'];
        return base_url() . $previewPath;
    }

    /**
     * 生成外链url (external_url)
     * @param $value
     * @param $data
     * @return string
     */
    public function getExternalUrlAttr($value, $data): string
    {
        // 存储方式本地：拼接当前域名
        if ($data['storage'] === 'local') {
            $data['domain'] = rtrim(uploads_url(), '/');
        }
        return "{$data['domain']}/{$data['file_path']}";
    }

    /**
     * 文件详情
     * @param int $fileId
     * @return static|array|null
     */
    public static function detail(int $fileId): array|static|null
    {
        return self::get($fileId);
    }


}