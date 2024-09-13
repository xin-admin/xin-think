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
namespace app\admin\controller;

use app\admin\model\admin\AdminGroupModel;
use app\admin\model\admin\AdminModel;
use app\admin\model\admin\AdminRuleModel;
use app\admin\validate\Admin as AdminVal;
use app\common\attribute\Auth;
use app\common\library\token\Token;
use Exception;
use think\response\Json;

class IndexController extends Controller
{

    public function initialize(): void
    {
        parent::initialize();
        $this->validate = new AdminVal();
    }

    /**
     * @return Json
     */
    public function index(): Json
    {
        $webSetting = get_setting('web');
        return $this->success(compact('webSetting'), '恭喜你已经成功安装 Xin Admin');
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function refreshToken(): Json
    {
        $token = $this->request->header('x-token');
        $reToken = $this->request->header('x-refresh-token');
        if ($this->request->isPost() && $reToken) {
            $Token = new Token;
            $Token->delete($token);
            $user_id = $Token->get($reToken)['user_id'];
            $token = md5(random_bytes(10));
            $Token->set($token, 'admin', $user_id);
            return $this->success(compact('token'));
        } else {
            return $this->error('请先登录！');
        }
    }

    /**
     * @return Json
     */
    public function login(): Json
    {
        if (!$this->request->isPost()) {
            return $this->warn('请求方法错误！');
        }

        $loginType = $this->request->post('loginType');

        // 账号密码登录
        if (isset($loginType) && $loginType === 'account') {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            // 规则验证
            $result = $this->validate->scene('account')->check([
                'username' => $username,
                'password' => $password
            ]);
            if (!$result) {
                return $this->warn($this->validate->getError());
            }

            $model = new AdminModel();
            $data = $model->login($username, $password);
            if ($data) {
                return $this->success($data);
            }
            return $this->error($model->getErrorMsg());
        }

        // 手机号登录
        if (isset($loginType) && $loginType === 'phone') {
            //            $mobile = $this->request->post('mobile');
            //            $captcha = $this->request->post('captcha');
            //
            //            $result = $this->validate->scene('phone')->check([
            //                'mobile' => $mobile,
            //                'captcha' => $captcha
            //            ]);
            //
            //            if (!$result) {
            //                return $this->warn($this->validate->getError());
            //            }
            return $this->error('暂不支持此方式登录');
        }

        return $this->warn('请选择登录方式！');

    }

    /**
     * 退出登录
     * @return Json
     */
    #[Auth]
    public function logout(): Json
    {
        $user_id = Auth::getAdminId();
        $admin = new AdminModel;
        if ($admin->logout($user_id)) {
            return $this->success('退出登录成功');
        } else {
            return $this->error($admin->getErrorMsg());
        }
    }

    /**
     * 获取管理员信息
     * @return Json
     * @throws Exception
     */
    #[Auth]
    public function getAdminInfo(): Json
    {
        $info = Auth::getAdminInfo();
        $model = new AdminGroupModel();
        $group = $model->where('id', $info['group_id'])->findOrEmpty()->toArray();
        $where = [];
        $where[] = ['status', '=', 1];
        $where[] = ['id', 'in', $group['rules']];
        $rule_model = new AdminRuleModel();
        // 权限
        $access = $rule_model->where($where)->column('key');
        // 菜单
        $where[] = ['show', '=', 1];
        $where[] = ['type', 'in', [0, 1]];
        $menus = $rule_model->where($where)->order('sort', 'desc')->select()->toArray();
        trace($menus);
        $menus = $this->getTreeData($menus);
        return $this->success(compact('menus', 'access', 'info'));
    }

}