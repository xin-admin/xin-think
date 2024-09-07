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
namespace app\common\attribute;

use app\admin\model\MonitorModel;
use Attribute;
use think\facade\Request;

/**
 * 请求注解类
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Monitor
{

    public function __construct(string $name = '')
    {
        $user_id = Auth::getAdminId();
        $controller = Request::controller();
        $action = Request::action();
        $ip = Request::ip();
        $url = Request::url();
        $host = Request::host();
        $data = json_encode(Request::post(), JSON_UNESCAPED_UNICODE);
        $params = json_encode(Request::param(), JSON_UNESCAPED_UNICODE);
        $create_time = time();
        MonitorModel::insert(compact('name','user_id','action','url','data','host','controller','ip','params','create_time'));
    }

}