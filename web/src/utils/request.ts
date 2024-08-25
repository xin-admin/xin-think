import {refreshAdminToken} from '@/services/admin';
import {refreshUserToken} from '@/services/api/user';
import type {RuntimeConfig} from '@umijs/max';
import {history, request} from '@umijs/max';
import {message, notification} from 'antd';

// 错误处理方案： 错误类型
enum ErrorShowType {
  SILENT = 0,
  WARN_MESSAGE = 1,
  ERROR_MESSAGE = 2,
  NOTIFICATION = 3,
  REDIRECT = 9,
}

const requestConfig: RuntimeConfig['request'] = {
  timeout: 5000,
  headers: { 'X-Requested-With': 'XMLHttpRequest' },
  // 请求拦截器
  requestInterceptors: [
    (config: any) => {
      let XToken = localStorage.getItem('x-token');
      let XUserToken = localStorage.getItem('x-user-token');
      if (XToken) {
        config.headers['x-token'] = XToken;
      }
      if (XUserToken) {
        config.headers['x-user-token'] = XUserToken;
      }
      return { ...config };
    },
  ],
  responseInterceptors: [
    [
      async function (response: any) {
        if(response.data.status === 202) {
          try {
            // 登录状态过期，刷新令牌并重新发起请求
            let app = localStorage.getItem('app');
            if( !app || app === 'app'){
              let res = await refreshUserToken()
              localStorage.setItem('x-user-token', res.data.token);
              response.headers!.xUserToken = res.data.token;
              // 重新发送请求
              return await request(response.config.url!, response.config);
            }else {
              let res = await refreshAdminToken()
              localStorage.setItem('x-token', res.data.token);
              response.headers!.xToken = res.data.token;
              // 重新发送请求
              let data = await request(response.config.url!,response.config);
              return Promise.resolve(data);
            }
          }catch (e) {
            return Promise.reject(e);
          }
        }
        return response;
      },
      async function (response: any) {
        let { success, status, msg, showType } = response.response.data;
        switch (status){
          case 500:
            // 服务器异常
            message.error(`服务器异常: ${status}`);
            return Promise.reject(response);
          case 401:
            // 没有登录拒绝访问
            localStorage.clear();
            history.push('/');
            return Promise.reject(response);
          case 403:
          case 400:
            // 预期用户操作错误
            if(success) return Promise.resolve(response);
            switch (showType) {
              case ErrorShowType.SILENT:
                // do nothing
                break;
              case ErrorShowType.WARN_MESSAGE:
                message.warning(msg);
                break;
              case ErrorShowType.ERROR_MESSAGE:
                message.error(msg);
                break;
              case ErrorShowType.NOTIFICATION:
                notification.open({
                  description: msg,
                  message: status,
                });
                break;
              case ErrorShowType.REDIRECT:
                // TODO: redirect
                break;
              default:
                message.error(msg);
            }
            return Promise.reject(response);
          default:
            message.error(`Response status:${response.data.status}`);
            return Promise.reject(response);
        }
      }
    ]
  ]
};

export default requestConfig;
