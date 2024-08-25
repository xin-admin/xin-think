import { request } from '@umijs/max';

const api = {
  loginApi: '/admin.php/index/login', // 用户登录
  logoutApi: '/admin.php/index/logout', // 退出登录
  getAdminInfoApi: '/admin.php/index/getAdminInfo', // 获取用户信息
  refreshAdminTokenApi: '/admin.php/index/refreshToken', // 刷新 Token
}

/**
 * 管理端用户登录
 * @param data
 * @constructor
 */
export async function adminLogin(data: USER.UserLoginFrom) {
  return request<USER.LoginResult>(api.loginApi, {
    method: 'post',
    data
  });
}

/**
 * 获取管理员用户信息
 * @constructor
 */
export async function getAdminInfo() {
  return request<USER.AdminInfoResult>(api.getAdminInfoApi, {
    method: 'get'
  });
}

/**
 * 刷新 Token
 * @constructor
 */
export async function refreshAdminToken() {
  return request<USER.ReToken>(api.refreshAdminTokenApi, {
    method: 'post',
    headers: {
      'x-refresh-token': localStorage.getItem('x-refresh-token') || ''
    }
  });
}

/**
 * 退出登录
 * @constructor
 */
export async function Logout() {
  return request<ResponseStructure<any>>(api.logoutApi, {
    method: 'post'
  });
}




