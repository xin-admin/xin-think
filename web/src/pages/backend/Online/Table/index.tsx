import {ProFormColumnsAndProColumns} from "@/components/XinTable/typings";
import XinTable from "@/components/XinTable";
import {Link} from '@umijs/max';
import {Access,useAccess} from "@umijs/max";

interface Data {
  id: number
  table_name: string
  update_time: string
  create_time: string
}

const api = '/online.online_table';

const OnlineTable = () => {
  const access = useAccess();

  const columns: ProFormColumnsAndProColumns<Data>[] = [
    {
      title: 'ID',
      dataIndex: 'id',
      hideInForm: true
    },
    {
      title: '表格名称',
      dataIndex: 'table_name',
      valueType: 'text',
    },
    {
      title: '描述',
      dataIndex: 'describe',
      valueType: 'text',
      hideInSearch: true
    },
    {
      title: '创建时间',
      dataIndex: 'create_time',
      valueType: 'date',
      hideInForm: true
    },
    {
      title: '修改时间',
      dataIndex: 'update_time',
      valueType: 'date',
      hideInForm: true
    },
  ];
  return (
    <>
      <XinTable<Data>
        tableApi={api}
        columns={columns}
        operateRender = { (record: Data) => {
          return (
            <Access accessible={access.buttonAccess('online.table.devise')}>
              <Link to={'/online/table/devise?id='+record.id}>设计页面</Link>
            </Access>
          )
        }}
      />
    </>
  )

}


export default OnlineTable
