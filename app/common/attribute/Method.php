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

namespace app\common\attribute;

use app\common\trait\RequestJson;
use Attribute;

/**
 * 请求注解类.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Method
{
    use RequestJson;

    public function __construct(string $method)
    {
        if (! $method) {
            return;
        }
        if (function_exists('request')) {
            $currentMethod = request()->method();
            if ($method == $currentMethod) {
                return;
            }
            $this->throwError('请求方式错误，请检查！');
        }
    }
}
