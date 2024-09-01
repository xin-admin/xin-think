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

namespace app\common\library\token;

use app\common\library\token\driver\Driver;
use InvalidArgumentException;
use think\facade\Config;
use think\helper\Str;

/**
 * Token 管理类.
 */
class Token
{
    // token 驱动类句柄
    public ?Driver $handler = null;

    // 驱动类命名空间
    protected string $namespace = '\\app\\common\\library\\token\\driver\\';

    /**
     * 获取驱动句柄.
     */
    public function getDriver(string $name = ''): mixed
    {
        if (! is_null($this->handler)) {
            return $this->handler;
        }
        // 默认驱动
        $name = $name ?: Config::get('xin.token.default');
        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }
        return $this->createDriver($name);
    }

    /**
     * 设置token.
     */
    public function set(string $token, string $type, int $user_id, ?int $expire = null): bool
    {
        return $this->getDriver()->set($token, $type, $user_id, $expire);
    }

    /**
     * 获取token.
     */
    public function get(string $token, bool $expirationException = true): array
    {
        return $this->getDriver()->get($token, $expirationException);
    }

    /**
     * 检查token.
     */
    public function check(string $token, string $type, int $user_id, bool $expirationException = true): bool
    {
        return $this->getDriver()->check($token, $type, $user_id, $expirationException);
    }

    /**
     * 删除token.
     */
    public function delete(string $token): bool
    {
        return $this->getDriver()->delete($token);
    }

    /**
     * 清理指定用户token.
     */
    public function clear(string $type, int $user_id): bool
    {
        return $this->getDriver()->clear($type, $user_id);
    }

    /**
     * 创建驱动句柄.
     */
    protected function createDriver(string $name): mixed
    {
        // 获取驱动配置
        $config = Config::get("xin.token.stores.{$name}");
        if (! $config) {
            throw new InvalidArgumentException("Store Config [{$name}] not found.");
        }
        $class = $this->resolveClass($name);
        return new $class($config);
    }

    /**
     * 获取驱动类.
     */
    protected function resolveClass(string $type): string
    {
        if ($this->namespace || str_contains($type, '\\')) {
            $class = str_contains($type, '\\') ? $type : $this->namespace . Str::studly($type);
            if (class_exists($class)) {
                return $class;
            }
        }
        throw new InvalidArgumentException("Driver [{$type}] not supported.");
    }
}
