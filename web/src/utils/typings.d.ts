// 与后端约定的响应数据格式
interface ResponseStructure {
  success: boolean
  data: any
  errorCode?: number
  msg?: string
  showType?: ErrorShowType
  status?: number
}