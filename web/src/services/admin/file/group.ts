import { request } from '@umijs/max';

const api = {
  list: '/admin.php/file.group/list',
  add: '/admin.php/file.group/add',
  edit: '/admin.php/file.group/edit',
  delete: '/admin.php/file.group/delete',
}

export async function GroupList() {
  return request<ResponseStructure<any>>(api.list, {
    method: 'get'
  });
}

export async function AddGroup(data: any) {
  return request<ResponseStructure<any>>(api.add, {
    method: 'post',
    data
  });
}

export async function EditGroup(data: any) {
  return request<ResponseStructure<any>>(api.edit, {
    method: 'put',
    data
  });
}

export async function DeleteGroup(data: any) {
  return request<ResponseStructure<any>>(api.delete, {
    method: 'delete',
    data
  });
}
