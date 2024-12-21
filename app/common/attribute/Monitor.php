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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://ip-api.com/json/' . $ip . '?lang=zh-CN');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $header[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        curl_close($ch);
        $resData = json_decode($response,true);
        if(!empty($resData['status']) && $resData['status'] == 'success') {
            return $resData['country'] . $resData['regionName'] . $resData['city'];
        }else {
            return '未知';
        }
    }

}