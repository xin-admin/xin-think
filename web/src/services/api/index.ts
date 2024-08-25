import { request } from '@umijs/max';

const api = {
  index: '/api.php/index',
  login: '/api.php/index/login',
  getMailCodeApi: '/api.php/index/sendMailCode'
}

/**
 * 获取网站信息
 * @constructor
 */
export async function index() {
  return request<API.ResponseStructure<any>>(api.index, {
    method: 'get'
  });
}

/**
 * 用户登录
 */
export async function login(data: USER.UserLoginFrom) {
  return request<USER.LoginResult>(api.login, {
    method: 'post',
    data
  });
}

/**
 * 获取邮箱验证码
 */
export async function getMailCode(data: any) {
  return request<USER.LoginResult>(api.getMailCodeApi, {
    method: 'post',
    data
  });
}
