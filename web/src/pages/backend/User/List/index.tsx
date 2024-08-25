// CRUD 一键生成
import XinTable from '@/components/XinTable'
import {ProFormColumnsAndProColumns} from '@/components/XinTable/typings';
import React from 'react';
import { UserOutlined } from '@ant-design/icons';
import { Avatar } from 'antd';
import XinDict from '@/components/XinDict';
import { useModel } from '@@/exports';

/**
 *  Api 接口
 */
const api = '/user.user'

/**
 *  数据类型
 */
interface Data {
  id: number;
  mobile: string;
  username: string;
  email: string;
  nickname: string;
  avatar: string;
  gender: string;
  birthday: string;
  money: string;
  score: string;
  motto: string;
  create_time: string;
  update_time: string;
  avatar_url: string;
}

/**
 * 表格渲染
 */
const User: React.FC = () => {
  const {getDictionaryData} = useModel('dictModel')
  const columns: ProFormColumnsAndProColumns<Data>[] =
  [
    {
      valueType:'digit',
      title:'ID',
      order:99,
      hideInForm: true,
      dataIndex:'id',
    },
    {
      valueType:'text',
      title:'手机号',
      order:98,
      dataIndex:'mobile',
    },
    {
      valueType:'text',
      title:'用户名',
      order:97,
      dataIndex:'username',
    },
    {
      valueType:'text',
      title:'用户邮箱',
      order:96,
      dataIndex:'email',
    },
    {
      valueType:'text',
      title:'昵称',
      order:95,
      dataIndex:'nickname',
    },
    {
      valueType:'text',
      title:'头像',
      order:94,
      dataIndex:'avatar_url',
      render: (_,data) => <Avatar src={data.avatar_url} style={{ backgroundColor: '#87d068' }} icon={<UserOutlined/>} />
    },
    {
      valueType:'text',
      title:'性别',
      order:93,
      request: async () => await getDictionaryData('sex'),
      render: (_, date) => <XinDict value={date.gender} dict={'sex'} />,
      dataIndex:'gender',
    },
    {
      valueType:'date',
      title:'生日',
      order:92,
      dataIndex:'birthday',
    },
    {
      valueType:'money',
      title:'余额',
      order:91,
      dataIndex:'money',
    },
    {
      valueType:'money',
      title:'积分',
      order:90,
      dataIndex:'score',
    },
    {
      valueType:'textarea',
      title:'签名',
      order:89,
      hideInSearch: true,
      hideInTable: true,
      dataIndex:'motto',
    },
    {
      valueType:'text',
      title:'密码',
      order:88,
      hideInSearch: true,
      hideInTable: true,
      hideInForm: true,
      dataIndex:'password',
    },
    // {
    //   valueType:'digit',
    //   title:'状态',
    //   order:2,
    //   hideInForm: true,
    //   dataIndex:'status',
    // },
    // {
    //   valueType:'date',
    //   title:'创建时间',
    //   order:1,
    //   hideInForm: true,
    //   dataIndex:'create_time',
    // },
    // {
    //   valueType:'date',
    //   title:'修改时间',
    //   hideInForm: true,
    //   dataIndex:'update_time',
    // },
  ]
  return (
    <XinTable<Data>
      tableApi={api}
      columns={columns}
      headerTitle={'用户列表'}
      addShow={false}
      operateShow={true}
      rowSelectionShow={true}
      editShow={false}
      deleteShow={true}
      accessName={'admin.rule'}
    />
  )

}

export default User
