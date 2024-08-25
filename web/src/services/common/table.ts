/**
 * Xin Table 公共接口
 */

import { request } from '@umijs/max';
interface XinApi {
   (
      url: string,
      params?: {
        keyword?: string;
        current?: number;
        pageSize?: number;
      } | { [key: string]: any },
      data?: { [key: string]: any },
      options?: { [key: string]: any }
    ): Promise<API.ResponseStructure<any>>
}

let app = localStorage.getItem('app') === 'app' ? '/api.php':'/admin.php'

/**
 * 公共查询接口
 * @param url
 * @param params
 * @param options
 */
export const listApi: XinApi = (url,params,options) =>  {
  return request<API.ResponseStructure<any>>(app+ url, {
    method: 'GET',
    params: {
      ...params,
    },
    ...(options || {}),
  });
}

/**
 * 公共新增接口
 * @param url
 * @param data
 * @param options
 */
export const addApi: XinApi = (url,data,options) => {
  return request<API.ResponseStructure<any>>(app+ url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    data: data,
    ...(options || {}),
  });
}

/**
 * 公共编辑接口
 * @param url
 * @param data
 * @param options
 */
export const editApi: XinApi = (url,data,options) =>  {
  return request<API.ResponseStructure<any>>(app+ url, {
    method: 'PUT',
    data: { ...data },
    ...(options || {}),
  });
}

/**
 * 公共删除接口
 * @param url
 * @param params
 * @param options
 */
export const deleteApi: XinApi = (url,params,options) => {
  return request<API.ResponseStructure<any>>(app+ url, {
    method: 'DELETE',
    params: { ...params },
    ...(options || {}),
  });
}

// interface XinApi {
//
//   url: string,
//   params?: {
//     keyword?: string;
//     current?: number;
//     pageSize?: number;
//   } | { [key: string]: any },
//   data?: { [key: string]: any },
//   options?: { [key: string]: any }
//
// }
//
// export function listApi<T>(props: XinApi){
//   const { url,params,options } = props
//   return request<API.ResponseStructure<T>>(url, {
//     method: 'GET',
//     params: {
//       ...params,
//     },
//     ...(options || {}),
//   });
// }
//
//
// export function addApi<T>(props: XinApi){
//   const { url,data,options } = props;
//   return request<API.ResponseStructure<T>>(url, {
//     method: 'POST',
//     headers: {
//       'Content-Type': 'application/json',
//     },
//     data: data,
//     ...(options || {}),
//   });
// }
//
// export function editApi<T>(props:XinApi){
//   const { url,params,options } = props
//   return request<API.ResponseStructure<T>>(url, {
//     method: 'GET',
//     params: { ...params },
//     ...(options || {}),
//   });
// }
//
// export function deleteApi<T>(props:XinApi){
//   const { url,params,options } = props;
//   return request<API.ResponseStructure<T>>(url, {
//     method: 'DELETE',
//     params: { ...params },
//     ...(options || {}),
//   });
// }
