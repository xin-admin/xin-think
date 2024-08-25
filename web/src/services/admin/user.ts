import { request } from '@umijs/max';

const api = {
  vagueSearchApi: '/admin.php/user.user/vagueSearch', // 搜索用户
  upUserMoneyApi: '/admin.php/user.userMoneyLog/add'
}

/**
 * 搜索用户
 */
export async function vagueSearchUser(params: {search: string | undefined}) {
  return request<API.TableData<USER.UserInfo>>(api.vagueSearchApi, {
    method: 'get',
    params
  });
}


/**
 * 更新用户余额
 */
export async function upUserMoney(data: {id: number,money: number,describe:string}) {
  return request<API.ResponseStructure<any>>(api.upUserMoneyApi, {
    method: 'post',
    data
  });
}
