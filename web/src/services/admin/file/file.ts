import { request } from '@umijs/max';

const api = {
  list: '/admin.php/file.file/list',
  add: '/admin.php/file.group/add',
  edit: '/admin.php/file.group/edit',
  delete: '/admin.php/file.file/delete',
}

export async function FileList(params: any) {
  return request<ResponseStructure<any>>(api.list, {
    method: 'get',
    params
  });
}

export async function DeleteFile(params: any) {
  return request<ResponseStructure<any>>(api.delete, {
    method: 'delete',
    params
  });
}

