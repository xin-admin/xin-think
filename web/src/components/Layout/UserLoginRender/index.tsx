import {
  AlipayOutlined,
  LockOutlined,
  MobileOutlined,
  QqOutlined,
  TaobaoOutlined,
  UserOutlined,
  WechatOutlined,
  WeiboOutlined,
} from '@ant-design/icons';
import { LoginForm, ProFormCaptcha, ProFormCheckbox, ProFormText } from '@ant-design/pro-components';
import { useModel } from '@umijs/max';
import { Divider, message, Space, Tabs } from 'antd';
import React, { useState } from 'react';
import { getMailCode, login } from '@/services/api';


export default () => {

  const { initialState, refresh } = useModel('@@initialState');
  const [loginType, setLoginType] = useState<USER.LoginType>('account');
  const handleSubmit = async (values: USER.UserLoginFrom) => {
    // 登录
    const msg = await login({ ...values, loginType });

    // 记录令牌
    await localStorage.setItem('x-user-token', msg.data.token);
    await localStorage.setItem('x-user-refresh-token', msg.data.refresh_token);
    await localStorage.setItem('app', 'admin');
    message.success('登录成功！');
    await refresh();
    window.location.href = '/'
    return;
  };

  const loginTypeItems = [
    {
      key: 'account',
      label: '账号密码登录'
    },
    {
      key: 'email',
      label: '邮箱登录'
    },
    {
      key: 'phone',
      label: '手机号登录'
    }
  ]

  return (
    <LoginForm
      logo={initialState!.webSetting.logo || 'https://file.xinadmin.cn/file/favicons.ico'}
      title={initialState!.webSetting.title || 'Xin Admin'}
      subTitle={initialState!.webSetting.subtitle || '用技术改变世界'}
      actions={
        <div
          style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            flexDirection: 'column',
          }}
        >
          <Divider plain>
              <span
                style={{ color: '#CCC', fontWeight: 'normal', fontSize: 14 }}
              >
                其他登录方式
              </span>
          </Divider>
          <Space align='center' size={24}>
            <QqOutlined style={{ fontSize: 20, color: '#4cafe9' }} />
            <WechatOutlined style={{ fontSize: 20, color: 'rgb(0,172,132)' }} />
            <AlipayOutlined style={{ fontSize: 20, color: '#1677FF' }} />
            <TaobaoOutlined style={{ fontSize: 20, color: '#FF6A10' }} />
            <WeiboOutlined style={{ fontSize: 20, color: '#e71f19' }} />
          </Space>
        </div>
      }
      onFinish={async (values) => {
        await handleSubmit(values as USER.UserLoginFrom);
      }}
    >
      <Tabs
        centered
        activeKey={loginType}
        onChange={(activeKey) => setLoginType(activeKey as USER.LoginType)}
        items={loginTypeItems}
      >
        </Tabs>
        {loginType === 'account' && (
          <>
            <ProFormText
              name="username"
              fieldProps={{
                size: 'large',
                prefix: <UserOutlined className={'prefixIcon'} />,
              }}
              placeholder={'用户名: user'}
              rules={[{required: true, message: '请输入用户名!',},]}
            />
            <ProFormText.Password
              name="password"
              fieldProps={{
                size: 'large',
                prefix: <LockOutlined className={'prefixIcon'} />,
              }}
              placeholder={'密码: 123456'}
              rules={[{required: true, message: '请输入密码！',},]}
            />
          </>
        )}
        {loginType === 'phone' && (
          <>
            <ProFormText
              fieldProps={{
                size: 'large',
                prefix: <MobileOutlined className={'prefixIcon'} />,
              }}
              name="mobile"
              placeholder={'手机号'}
              rules={[
                {
                  required: true,
                  message: '请输入手机号！',
                },
                {
                  pattern: /^1\d{10}$/,
                  message: '手机号格式错误！',
                },
              ]}
            />
            <ProFormCaptcha
              fieldProps={{
                size: 'large',
                prefix: <LockOutlined className={'prefixIcon'} />,
              }}
              phoneName={'mobile'}
              captchaProps={{
                size: 'large',
              }}
              placeholder={'请输入验证码'}
              captchaTextRender={(timing, count) => {
                if (timing) {
                  return `${count} ${'获取验证码'}`;
                }
                return '获取验证码';
              }}
              name="captcha"
              rules={[
                {
                  required: true,
                  message: '请输入验证码！',
                },
              ]}
              onGetCaptcha={async () => {
                message.success('获取验证码成功！验证码为：1234');
              }}
            />
          </>
        )}
        {loginType === 'email' && (
          <>
            <ProFormText
              fieldProps={{
                size: 'large',
                prefix: <MobileOutlined className={'prefixIcon'} />,
              }}
              name="email"
              placeholder={'邮箱'}
              rules={[
                {
                  required: true,
                  message: '请输入邮箱！',
                },
                {
                  pattern: /^[\w\\.-]+@[\w\\.-]+\.\w+$/,
                  message: '邮箱格式错误！',
                },
              ]}
            />
            <ProFormCaptcha
              fieldProps={{
                size: 'large',
                prefix: <LockOutlined className={'prefixIcon'} />,
              }}
              phoneName={'email'}
              captchaProps={{
                size: 'large',
              }}
              placeholder={'请输入验证码'}
              captchaTextRender={(timing, count) => {
                if (timing) {
                  return `${count} ${'获取验证码'}`;
                }
                return '获取验证码';
              }}
              name="captcha"
              rules={[
                {
                  required: true,
                  message: '请输入验证码！',
                },
              ]}
              onGetCaptcha={async (email) => {
                await getMailCode({email});
                message.success('获取验证码成功！');
              }}
            />
          </>
        )}
        <div style={{marginBlockEnd: 24}}>
          <ProFormCheckbox noStyle name="autoLogin">自动登录</ProFormCheckbox>
          <Space style={{float: 'right'}}>
            <a>忘记密码</a>
          </Space>
        </div>
      </LoginForm>
  );
};

