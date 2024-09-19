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
namespace app\common\trait;

use app\common\enum\ApiEnum\ShowType as ShopTypeEnum;
use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;

/**
 * 响应 trait
 * 支持 throw 响应
 */
trait RequestJson
{

    /**
     *  成功响应
     * @param string|array $data 响应数据
     * @param string $message 响应内容
     * @return Json
     */
    protected function success(string | array $data = [], string $message = 'ok'): Json
    {
        if(is_array($data)) {
            return self::renderJson(true, $data, $message);
        }
        return self::renderJson(true, [], $data);
    }

    /**
     * 抛出成功响应，中断程序运行
     * @param string|array $data 响应数据
     * @param string $message 响应内容
     * @return void
     */
    protected function throwSuccess(string | array $data = [], string $message = 'ok'): void
    {
        if(is_array($data)) {
            self::renderThrow(true, $data, $message);
        }
        self::renderThrow(true, [], $data);
    }

    /**
     *  返回失败响应
     * @param string|array $data 响应数据
     * @param string $message 响应内容
     * @return Json
     */
    protected function error(string | array $data = [], string $message = ''): Json
    {
        if(is_array($data)) {
            return self::renderJson(false, $data, $message, ShopTypeEnum::ERROR_MESSAGE);
        }
        return self::renderJson(false, [], $data, ShopTypeEnum::ERROR_MESSAGE);
    }

    /**
     * 抛出失败响应，中断程序运行
     * @param string|array $data
     * @param string $message
     * @return void
     */
    protected function throwError(string | array $data = [], string $message = ''): void
    {
        if(is_array($data)) {
            self::renderThrow(false, $data, $message, ShopTypeEnum::ERROR_MESSAGE);
        }
        self::renderThrow(false, [], $data, ShopTypeEnum::ERROR_MESSAGE);
    }

    /**
     *  返回警告响应
     * @param string|array $data 响应数据
     * @param string $message 响应内容
     * @return Json
     */
    protected function warn(string | array $data = [], string $message = ''): Json
    {
        if(is_array($data)) {
            return self::renderJson(false, $data, $message, ShopTypeEnum::WARN_MESSAGE);
        }
        return self::renderJson(false, [], $data, ShopTypeEnum::WARN_MESSAGE);
    }

    /**
     * 抛出警告响应，中断程序运行
     * @param string|array $data
     * @param string $message
     * @return void
     */
    protected function throwWarn(string | array $data = [], string $message = ''): void
    {
        if(is_array($data)) {
            self::renderThrow(false, $data, $message, ShopTypeEnum::WARN_MESSAGE);
        }
        self::renderThrow(false, [], $data, ShopTypeEnum::WARN_MESSAGE);
    }

    /**
     * 通知响应
     * @param string $msg 通知标题
     * @param string $description 通知描述
     * @param string $placement 通知位置 top topLeft topRight bottom bottomLeft bottomRight
     * @param ShopTypeEnum $showTypeEnum 通知类型
     * @return Json
     */
    protected function notification(
        string $msg,
        string $description,
        ShopTypeEnum $showTypeEnum = ShopTypeEnum::SUCCESS_NOTIFICATION,
        string $placement = 'topLeft'
    ): Json
    {
        $showType = $showTypeEnum->value;
        $success = false;
        return json(compact('description', 'success', 'msg', 'showType', 'placement'));
    }

    /**
     *  返回 Json 响应
     * @param bool $success 响应状态
     * @param array $data 响应数据
     * @param string $msg 响应内容
     * @param ShopTypeEnum $showTypeEnum
     * @return Json
     */
    protected static function renderJson(
        bool $success = true,
        array $data = [],
        string $msg = '',
        ShopTypeEnum $showTypeEnum = ShopTypeEnum::SUCCESS_MESSAGE
    ): Json
    {
        $showType = $showTypeEnum->value;
        return json(compact('data', 'success', 'msg', 'showType'));
    }

    /**
     *  抛出 API 数据
     * @param bool $success
     * @param mixed $data 返回数据
     * @param string $msg
     * @param ShopTypeEnum $showTypeEnum
     */
    public static function renderThrow(
        bool $success = true,
        array $data = [],
        string $msg = '',
        ShopTypeEnum $showTypeEnum = ShopTypeEnum::SUCCESS_MESSAGE
    ){
        $showType = $showTypeEnum->value;
        $response = Response::create(compact('data', 'success', 'msg', 'showType'), 'json');
        throw new HttpResponseException($response);
    }

}
