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

namespace app\common\library\storage;

use app\common\library\storage\driver\Driver;
use InvalidArgumentException;
use think\facade\Config;
use think\helper\Str;

/**
 * 储存系统
 */
class Storage
{
    // 当前存储引擎类
    public ?Driver $engine = null;

    // 当前存储引擎
    protected string $storage;

    // 驱动类命名空间
    protected string $namespace = '\\app\\common\\library\\storage\\driver\\';

    private mixed $config;  // upload 配置

    /**
     * 构造方法.
     * @param string $storage 储存引擎
     */
    public function __construct(string $storage = '')
    {
        $this->storage = $storage;
        $this->config = Config::get('xin.storage');
        $this->engine = $this->getDriver();
    }

    /**
     * 获取驱动句柄.
     */
    public function getDriver(): mixed
    {
        if (! is_null($this->engine)) {
            return $this->engine;
        }
        $storage = empty($this->storage) ? $this->config['default'] : $this->storage;
        $class = $this->namespace . Str::studly($storage);
        if (class_exists($class)) {
            return new $class($storage, $this->config['engine'][$storage]);
        }
        throw new InvalidArgumentException("Storage Driver [{$storage}] not supported.");
    }

    /**
     * 设置磁盘配置.
     */
    public function setDisk(string $disk): void
    {
        $this->engine->setDisk($disk);
    }

    /**
     * 设置上传文件的验证规则.
     */
    public function setValidationScene(string $rules = ''): void
    {
        $this->engine->setValidationScene($rules);
    }

    /**
     * 设置上传的文件信息.
     */
    public function setUploadFile(string $name): void
    {
        $this->engine->setUploadFile($name);
    }

    /**
     * 执行文件上传.
     */
    public function upload(): bool
    {
        return $this->engine->upload();
    }

    /**
     * 执行文件删除.
     */
    public function delete(array $fileInfo): bool
    {
        return $this->engine->delete($fileInfo);
    }

    /**
     * 获取错误信息.
     */
    public function getError(): string
    {
        return $this->engine->getError();
    }

    /**
     * 返回保存的文件信息.
     */
    public function getSaveFileInfo(): array
    {
        return $this->engine->getSaveFileInfo();
    }
}
