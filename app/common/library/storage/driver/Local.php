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

namespace app\common\library\storage\driver;

use Exception;
use think\facade\Config;
use think\facade\Filesystem;

/**
 * 本地文件驱动
 * Class Local.
 */
class Local extends Driver
{
    /**
     * 上传图片文件.
     */
    public function upload(): bool
    {
        // 验证文件类型
        if (! $this->validate()) {
            return false;
        }
        try {
            $filePath = $this->getSaveFileInfo()['file_path'];
            $hashRoute = dirname($filePath);
            $hashName = basename($filePath);
            // 上传到本地服务器
            $sts = Filesystem::disk($this->disk)->putFileAs($hashRoute, $this->files, $hashName);
            return (bool) $sts;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件.
     */
    public function delete(array $fileInfo): bool
    {
        $fileRootPath = Config::get("filesystem.disks.{$fileInfo['domain']}.root");
        // 文件所在目录
        $realPath = realpath($fileRootPath . '/' . $fileInfo['file_path']);
        return $realPath === false || unlink($realPath);
    }
}
