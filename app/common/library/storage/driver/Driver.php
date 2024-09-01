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

use app\common\enum\FileType;
use app\common\trait\RequestJson;
use Exception;
use think\facade\Request;
use think\file\UploadedFile;

/**
 * 存储引擎抽象类
 * Class Basics.
 */
abstract class Driver
{
    use RequestJson;

    // 错误信息
    protected string $error;

    // 存储配置
    protected ?array $config;

    // file对象句柄
    protected array|UploadedFile $files;

    // 验证规则
    protected string $validateRuleScene;

    // 磁盘配置
    protected string $disk = 'public';

    // 当前存储引擎
    protected string $storage;

    /**
     * 构造函数
     * Server constructor.
     * @param string $storage 存储方式
     * @param null|array $config 存储配置
     */
    public function __construct(string $storage, ?array $config = null)
    {
        $this->storage = $storage;
        $this->config = $config;
    }

    /**
     * 设置磁盘配置.
     */
    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    /**
     * 设置上传文件的验证规则.
     */
    public function setValidationScene(string $scene = ''): void
    {
        $this->validateRuleScene = $scene;
    }

    /**
     * 设置上传的文件信息.
     */
    public function setUploadFile(string $name): void
    {
        // 接收上传的文件
        try {
            $this->files = Request::file($name);
        } catch (Exception $e) {
            $maxSize = ini_get('upload_max_filesize');
            $myMsg = $e->getCode() === 1 ? "上传的文件超出了服务器最大限制: {$maxSize}；可修改php.ini文件中upload_max_filesize项调整" : false;
            $this->throwError($myMsg ?: $e->getMessage());
        }
        empty($this->files) && $this->throwError('未找到上传文件的信息');
    }

    /**
     * 生成保存的文件信息.
     */
    public function getSaveFileInfo(): array
    {
        // 获取 hash 文件名
        $hashName = $this->hashName($this->files);
        // 存储目录
        $filePath = $this->getFilePath($hashName);
        // 文件名称
        $fileName = $this->files->getOriginalName();
        // 文件扩展名
        $fileExt = strtolower($this->files->extension());
        return [
            'storage' => $this->storage,                            // 存储方式
            'domain' => $this->config['domain'] ?? $this->disk, // 存储域名
            'file_path' => $filePath,                           // 文件路径
            'file_name' => $fileName,                           // 文件名称
            'file_size' => $this->files->getSize(),             // 文件大小(字节)
            'file_ext' => $fileExt,                             // 文件扩展名
        ];
    }

    /**
     * 返回错误信息.
     * @return mixed
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 验证上传的文件.
     */
    protected function validate(): bool
    {
        $file_ext = $this->files->extension();
        $file_size = $this->files->getSize();

        $fileValidate = FileType::data()[$this->validateRuleScene];
        // 验证文件大小
        if ($file_size > $fileValidate['max_size']) {
            $this->error = '上传文件大小超限制！';
            return false;
        }
        // 验证扩展名
        if ($fileValidate['file_ext'] === '*') {
            return true;
        }
        if (is_array($fileValidate['file_ext'])) {
            if (in_array($file_ext, $fileValidate['file_ext'])) {
                return true;
            }
            $this->error = "不支持的{$fileValidate['name']}类型";
            return false;
        }
        if ($fileValidate['file_ext'] === $file_ext) {
            return true;
        }
        $this->error = "不支持的{$fileValidate['name']}类型";
        return false;
    }

    /**
     * 文件上传.
     */
    abstract protected function upload(): bool;

    /**
     * 文件删除.
     */
    abstract protected function delete(array $fileInfo): bool;

    /**
     * 生成 hash 文件名.
     */
    private function hashName(UploadedFile $file): string
    {
        return $file->hashName(function () {
            return date('Ymd') . DIRECTORY_SEPARATOR . md5(uniqid((string) mt_rand(), true));
        });
    }

    /**
     * 获取hashName的路径.
     */
    private function getFilePath(string $hashName): string
    {
        return convert_left_slash($hashName);
    }
}
