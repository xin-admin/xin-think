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
use app\common\enum\ApiEnum\StatusCode;
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

        $app = app('http')->getName();
        if ( $app === 'app' ) {
            $token = self::getUserToken();
        } else {
            $token = self::getToken();
        }
        $tokenData = self::getTokenData($token);
        if ( $tokenData['type'] != $app ) {
            self::throwError('Token 类型不正确！');
        }
        $rules = [];
        if ($tokenData['type'] == 'admin') {
            $adminInfo = self::getAdminInfo();
            if ($adminInfo['group_id'] == 1) {
                return;
            }
            if (!$adminInfo['status']) self::throwError('账户已被禁用！');
            // 获取用户所有权限
            $group = (new AdminGroupModel())->where('id', $adminInfo['group_id'])->findOrEmpty();
            $rules = (new AdminRuleModel())->where('id', 'in', $group->rules)->column('key');
            $rules = array_map('strtolower',$rules);
        }
        if ($tokenData['type'] == 'user') {
            $userInfo = self::getUserInfo();
            if (!$userInfo['status']) self::throwError('账户已被禁用！');
            $group = (new UserGroupModel())->where('id', $userInfo['group_id'])->findOrEmpty();
            $rules = (new UserRuleModel())->where('id', 'in', $group->rules)->column('key');
            $rules = array_map('strtolower',$rules);
        }

        // 使用反射机制获取当前控制器的 AuthName
        $class = 'app\\' . $app . '\\controller\\' . str_replace(".", "\\", request()->controller());
        $reflection = new ReflectionClass($class);
        $properties = $reflection->getProperty('authName')->getDefaultValue();
        $allowAction = $reflection->getProperty('allowAction')->getDefaultValue(); // 权限验证白名单
        if (in_array(request()->controller(), $allowAction)) {
            return;
        }
        if ($properties) {
            $authKey = strtolower($properties . '.' . $key);
        } else {
            $authKey = strtolower(str_replace("\\", ".", request()->controller()) . '.' . $key);
        }
        trace($authKey);
        if (!in_array($authKey, $rules)) {
            self::throwError('暂无权限！');
        }

    }

    /**
     * 是否登录
     * @return string
     */
    static public function getUserToken(): string
    {
        $token = request()->header('x-user-token');
        if (!$token) {
            static::throwError('请先登录！');
        }
        return $token;
    }

    /**
     * 是否登录
     * @return string
     */
    static public function getToken(): string
    {
        $token = request()->header('x-token');
        if (!$token) {
            static::throwError('请先登录！');
        }
        return $token;
    }


    /**
     * 是否登录 Api
     * @return bool
     */
    static public function isUserLogin(): bool
    {
        $token = request()->header('x-user-token');
        if ($token) return true;
        return false;
    }

    /**
     * 是否登录 Admin
     * @return bool
     */
    static public function isLogin(): bool
    {
        $token = request()->header('x-token');
        if ($token) return true;
        return false;
    }

    /**
     * 获取 Token Data
     * @param $token
     * @return array
     */
    static public function getTokenData($token): array
    {
        $tokenData = (new Token)->get($token);
        if (!$tokenData) {
            static::throwError('请先登录！');
        }
        return $tokenData;
    }

    /**
     * 获取用户ID
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
     * 获取用户信息
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
     * 获取用户信息
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
     * @param string $msg
     * @return void
     */
    static private function throwError(string $msg = ''): void
    {
        $data = [
            'data' => ['type' => 'user'],
            'success' => false,
            'status' => StatusCode::RULE->value,
            'msg' => $msg,
            'showType' => ShowType::ERROR_MESSAGE->value
        ];
        $response = Response::create($data, 'json', StatusCode::RULE->value);
        throw new HttpResponseException($response);
    }

}