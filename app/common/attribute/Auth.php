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

use app\admin\model\admin\AdminGroupModel;
use app\admin\model\admin\AdminModel as AdminModel;
use app\admin\model\admin\AdminRuleModel;
use app\api\model\UserModel as UserModel;
use app\common\enum\ApiEnum\ShowType;
use app\common\library\token\Token;
use app\common\model\user\UserGroupModel;
use app\common\model\user\UserRuleModel;
use Attribute;
use Exception;
use ReflectionClass;
use think\exception\HttpResponseException;
use think\Response;

/**
 * 接口权限注解
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Auth
{
    /**
     * @var string
     */
    public string $token;

    /**
     * 权限初始化，获取请求用户验证权限
     * @param string $key
     * @throws Exception
     */
    public function __construct(string $key = '')
    {
        if(!function_exists('app')) return;
        // 获取权限标识
        $rules = self::getRules();
        if(empty($key)) return;
        // 使用反射机制获取当前控制器的 AuthName
        $appName = app('http')->getName();
        $controllerName = request()->controller();
        $class = 'app\\' . $appName . '\\controller\\' . str_replace(".", "\\", $controllerName . 'Controller');
        $reflection = new ReflectionClass($class);
        $authName = $reflection->getProperty('authName')->getDefaultValue();
        if (!$authName) {
            $authName = str_replace("\\", ".", $controllerName);
        }
        $authKey = strtolower($authName . '.' . $key);
        // 权限不存在添加权限
        $authList = (new AdminRuleModel)->column('key');
        if(!in_array($authKey, $authList)) {
            self::addAuth($key, $authName);
        }
        if (!in_array($authKey, $rules)) {
            $data = [
                'success' => false,
                'msg' => '暂无权限',
                'showType' => ShowType::WARN_NOTIFICATION->value,
                'description' => '请联系管理员获取权限，如果你是管理员请检查权限菜单中是否有本接口的权限！'
            ];
            $response = Response::create($data, 'json');
            throw new HttpResponseException($response);
        }
    }

    /**
     * 获取用户ID，未登录抛出错误
     * @return int
     */
    static public function getUserId(): int
    {
        $token = self::getUserToken();
        $tokenData = self::getTokenData($token);
        if ($tokenData['type'] != 'user' || !isset($tokenData['user_id'])) {
            self::throwError('用户ID不存在！');
        }
        return $tokenData['user_id'];
    }

    /**
     * 获取用户信息（用户端）未登录抛出错误
     * @return array
     */
    public static function getUserInfo(): array
    {
        $user_id = self::getUserId();
        $userModel = new UserModel;
        $user = $userModel->where('id', $user_id)->with(['avatar'])->findOrEmpty();
        if ($user->isEmpty()) {
            self::throwError('用户不存在！');
        }
        return $user->toArray();
    }

    /**
     * 获取管理员ID
     * @return int
     */
    static public function getAdminId(): int
    {
        $token = self::getToken();
        $tokenData = self::getTokenData($token);
        if ($tokenData['type'] != 'admin' || !isset($tokenData['user_id'])) {
            self::throwError('管理员ID不存在！');
        }
        return $tokenData['user_id'];
    }

    /**
     * 获取管理员信息（管理端）未登录抛出错误
     * @return array
     */
    public static function getAdminInfo(): array
    {
        $user_id = self::getAdminId();
        $userModel = new AdminModel;
        $user = $userModel->where('id', $user_id)->with(['avatar'])->findOrEmpty();
        if ($user->isEmpty()) {
            self::throwError('用户不存在！');
        }
        return $user->toArray();
    }

    /**
     * 获取 Token （用户端）未登录抛出错误
     * @return string
     */
    static private function getUserToken(): string
    {
        $token = request()->header('x-user-token');
        if (!$token) {
            self::throwError('请先登录！');
        }
        return $token;
    }

    /**
     * 获取 Token （管理端） 未登录抛出错误
     * @return string
     */
    static private function getToken(): string
    {
        $token = request()->header('x-token');
        if (!$token) {
            self::throwError('请先登录！');
        }
        return $token;
    }

    /**
     * 获取 Token Data
     * @param $token
     * @return array
     */
    static private function getTokenData($token): array
    {
        $tokenData = (new Token)->get($token);
        if (!$tokenData) {
            self::throwError('请先登录！');
        }
        return $tokenData;
    }

    /**
     * 获取权限
     * @return array
     */
    static private function getRules(): array
    {
        $appName = app('http')->getName();
        if ( $appName == 'admin' ) {
            $token = self::getToken();
        } else {
            $token = self::getUserToken();
        }
        $tokenData = self::getTokenData($token);
        $rules = [];
        if ($tokenData['type'] == 'user' && $appName == 'app') {
            $userInfo = self::getUserInfo();
            if (!$userInfo['status']) self::throwError('账户已被禁用！');
            $group = (new UserGroupModel())->where('id', $userInfo['group_id'])->findOrEmpty();
            $rules = (new UserRuleModel())->where('id', 'in', $group->rules)->column('key');
            $rules = array_map('strtolower',$rules);
        }else if($tokenData['type'] == 'admin' && $appName == 'admin') {
            $adminInfo = self::getAdminInfo();
            if (!$adminInfo['status']) self::throwError('账户已被禁用！');
            $group = (new AdminGroupModel())->where('id', $adminInfo['group_id'])->findOrEmpty();
            $rules = (new AdminRuleModel())->where('id', 'in', $group->rules)->column('key');
            $rules = array_map('strtolower',$rules);
        }else {
            self::throwError('Token 类型错误！');
        }
        return $rules;
    }

    /**
     * 权限验证错误
     * @param string $msg
     * @return void
     */
    static private function throwError(string $msg = ''): void
    {
        $data = ['success' => false, 'msg' => $msg];
        $response = Response::create($data, 'json', 401);
        throw new HttpResponseException($response);
    }

    /**
     * 如果是新写的接口，权限不存在，自动添加按钮/接口权限
     * @param string $key
     * @param string $authName
     * @return void
     */
    static private function addAuth(string $key, string $authName): void
    {
        $model = new AdminRuleModel();
        $p_auth = $model->where('key', $authName)->findOrEmpty();
        if($p_auth->isEmpty()){
            return;
        }
        $model->insert([
            'name' => '新接口',
            'sort' => 0,
            'type' => 2,
            'pid' => $p_auth->id,
            'key' => $authName . '.' . $key
        ]);
        $data = [
            'success' => false,
            'msg' => '权限更新',
            'showType' => ShowType::WARN_NOTIFICATION->value,
            'description' => '权限自动更新，请刷新页面重试~'
        ];
        $response = Response::create($data, 'json');
        throw new HttpResponseException($response);
    }
}