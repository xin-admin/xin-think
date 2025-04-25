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
 * 系统监控注解类
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Monitor
{

    /**
     * 系统监控注解
     * @param string $name 接口名称
     * @param bool $auth 记录权限
     * @param string $user_id 用户ID
     */
    public function __construct(string $name = '', bool $auth = true, string $user_id = '')
    {
        if($auth) {
            $user_id = Auth::getAdminId();
        }
        $controller = Request::controller();
        $action = Request::action();
        $ip = Request::ip();
        $address = $this->getMethod($ip);
        $url = Request::url();
        $host = Request::host();
        $data = json_encode(Request::post(), JSON_UNESCAPED_UNICODE);
        $params = json_encode(Request::get(), JSON_UNESCAPED_UNICODE);
        $create_time = time();
        MonitorModel::insert(compact('name','address','user_id','action','url','data','host','controller','ip','params','create_time'));
    }

    /**
     * 获取请求IP省市县
     */
    public function getMethod($ip): string
    {
        if($ip == '127.0.0.1') {
            return '本地';
        }
        // 这里可以使用第三方 API（如 IPStack 或 IPInfo）来获取地理位置
        try {
            $response = file_get_contents("https://ipinfo.io/{$ip}/json");
            $data = json_decode($response, true);
            return $data['city'] . ', ' . $data['country'];
        }catch (\Exception $e) {
            return 'XXX';
        }
    }

}