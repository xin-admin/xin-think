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
declare (strict_types=1);

namespace app;

use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\trait\RequestJson;
use think\App;
use think\db\exception\PDOException;
use think\facade\Db;


/**
 * 控制器基础类
 */
abstract class BaseController
{
    use RequestJson;

    /**
     * Request实例
     * @var \think\Request
     */
    protected \think\Request $request;

    /**
     * 应用实例
     * @var App
     */
    protected App $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected array $middleware = [];

    /**
     * 控制器名称
     * @var string
     */
    protected string $controller;

    /**
     * 方法名称
     * @var string
     */
    protected string $action;

    /**
     * 当前uri
     * @var string
     */
    protected string $routeUri;

    /**
     * 当前类权限标识
     * @var string
     */
    protected string $authName;

    // 权限验证白名单
    protected array $allowAction = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize(): void
    {
        // 检测数据库连接
        try {
            Db::execute("SELECT 1");
        } catch (PDOException $e) {
            $this->throwError($e->getMessage());
        }
        // 获取请求路由信息
        $this->getRouteInfo();
        // 运行注解
        $ref = new \ReflectionObject($this);
        try {
            $attrs = $ref->getMethod($this->action)->getAttributes();
            foreach ($attrs as $attr) {
                if($attr->getName() === Auth::class) {
                    $attr->newInstance();
                }
                if($attr->getName() === Method::class) {
                    $attr->newInstance();
                }
            }
        } catch (\ReflectionException $e) {
            $this->throwError('当前方法未找到');
        }

    }

    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteInfo(): void
    {
        $this->controller = uncamelize($this->request->controller());
        $this->action = $this->request->action();
        // 当前uri
        $this->routeUri = "{$this->controller}/$this->action";
    }

    /**
     * 获取树状列表
     * @param $list
     * @param int $parentId
     * @return array
     */
    protected function getTreeData(&$list, int $parentId = 0): array
    {
        $data = [];
        foreach ($list as $key => $item) {
            if ($item['pid'] == $parentId) {
                $children = $this->getTreeData($list, $item['id']);
                !empty($children) && $item['children'] = $children;
                $data[] = $item;
                unset($list[$key]);
            }
        }
        return $data;
    }

}
