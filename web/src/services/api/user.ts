import { request } from '@umijs/max';

const api = {
  getUserInfoApi: '/api.php/user/getUserInfo',
  loginApi: '/api.php/index/login',
  logoutApi: '/api.php/user/logout',
  setUserApi: '/api.php/user/setUserInfo',
  setPwdApi: '/api.php/user/setPassword',
  reTokenApi: '/api.php/user/refreshToken',
  getMoneyLogApi: '/api.php/user/getMoneyLog',
}

/**
 * 获取网站信息
 * @constructor
 */
export async function getUserInfo() {
  return request<API.ResponseStructure<USER.UserInfoResult>>(api.getUserInfoApi, {
    method: 'get'
  });
}


/**
 * 刷新 Token
 * @constructor
 */
export async function refreshUserToken() {
  return request<USER.ReToken>(api.reTokenApi, {
    method: 'post',
    headers: {
      'x-user-refresh-token': localStorage.getItem('x-user-refresh-token') || ''
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


/**
 * 设置用户信息
 * @constructor
 */
export async function setUserInfo(data: USER.UserInfo) {
  return request<ResponseStructure<any>>(api.setUserApi, {
    method: 'post',
    data
  });
}

/**
 * 设置密码
 * @constructor
 */
export async function setPassWord(data: USER.UpdatePassword) {
  return request<ResponseStructure<any>>(api.setPwdApi, {
    method: 'post',
    data
  });
}

/**
 * 获取积分记录
 * @constructor
 */
export async function getMoneyLog(params: any) {
  return request<ResponseStructure<any>>(api.getMoneyLogApi, {
    method: 'get',
    params
  });
}
