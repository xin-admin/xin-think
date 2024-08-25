/**
 * 系统服务接口
 */
import { request } from '@umijs/max';

/**
 * 获取系统字典
 */
export const gitDict = () => {
  return request<API.ResponseStructure<any>>('/admin.php/system.dictItem/dictList', {
    method: 'get',
  })
}

/**
 * 获取设置分组
 */
export const getSettingGroup = () => {
  return request<API.ResponseStructure<any>>('/admin.php/system.setting/querySettingGroup', {
    method: 'get',
  })
}

/**
 * 添加设置分组
 * @param data
 */
export const addGroup = (data: {
  key: string,
  title: string,
  pid?: number
}) => {
  return request<API.ResponseStructure<any>>('/admin.php/system.setting/addGroup', {
    method: 'post',
    data
  })
}

/**
 * 保存设置
 */
export const saveSetting = (data: any) => {
  return request<API.ResponseStructure<any>>('/admin.php/system.setting/saveSetting', {
    method: 'post',
    data
  })
}
