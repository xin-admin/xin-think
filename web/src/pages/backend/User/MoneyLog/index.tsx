// CRUD 一键生成
import XinTable from '@/components/XinTable'
import {ProFormColumnsAndProColumns} from '@/components/XinTable/typings';
import React from 'react';
import { UserOutlined } from '@ant-design/icons';
import { Avatar, Space, Popover } from 'antd';
import XinDict from '@/components/XinDict';
import { useModel } from '@umijs/max';
import AddMoneyLog from './components/AddMoneyLog';

/**
 *  Api 接口
 */
const api = '/user.userMoneyLog'

/**
 *  数据类型
 */
interface Data {
  id: number;
  user_id: string;
  user: USER.UserInfo;
  scene: string;
  money: string;
  describe: string;
  remark: string;
  create_time: string;
}

/**
 * 表格渲染
 */
const User: React.FC = () => {
  const {getDictionaryData} = useModel('dictModel')

  const userInfo = (info: USER.UserInfo) => {
    return (
      <>
        <p>用户ID：{info.id}</p>
        <p>用户昵称：{info.nickname}</p>
        <p>用户名：{info.username}</p>
        <p>用户邮箱：{info.email}</p>
        <p>用户余额：{info.money} ￥</p>
      </>
    )
  }

  const columns: ProFormColumnsAndProColumns<Data>[] =
    [
      {
        valueType:'digit',
        title:'记录ID',
        order:99,
        hideInForm: true,
        dataIndex:'id',
      },
      {
        valueType:'text',
        title:'用户 ID',
        order:98,
        dataIndex:'user_id',
        hideInForm: true,
        hideInTable: true,
      },
      {
        valueType:'text',
        title:'用户',
        order:98,
        dataIndex:'user_Id',
        search: false,
        render: (_, date) => (
          <Popover placement="left" content={userInfo(date.user)} title={date.user.nickname}>
            <Space style={{ display: 'flex' }}>
              <Avatar src={date.user.avatar_url} icon={<UserOutlined/>}></Avatar>
              {date.user.nickname}
            </Space>
          </Popover>

        )
      },
      {
        valueType:'text',
        title:'类型',
        order:93,
        dataIndex:'scene',
        request: async () => getDictionaryData('moneyLog'),
        render: (_, date) => <XinDict value={date.scene} dict={'moneyLog'} />
      },
      {
        valueType:'money',
        title:'变动金额',
        order:91,
        dataIndex:'money',
        search: false
      },
      {
        valueType:'text',
        title:'描述/备注',
        order:90,
        search: false,
        dataIndex:'describe',
      },
      {
        valueType:'date',
        title:'创建时间',
        order:1,
        hideInForm: true,
        dataIndex:'create_time',
      }
    ]
  return (
    <XinTable<Data>
      tableApi={api}
      columns={columns}
      headerTitle={'用户余额变动记录'}
      addShow={false}
      operateShow={true}
      rowSelectionShow={true}
      editShow={false}
      deleteShow={true}
      accessName={'admin.rule'}
      toolBarRender={()=>[<AddMoneyLog key={'add'}/>]}
    />
  )

}

export default User
