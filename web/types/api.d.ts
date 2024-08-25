declare namespace API {
  interface Params {
    /** keyword */
    keyword?: string;
    /** current */
    current?: number;
    /** pageSize */
    pageSize?: number;
  }

  interface ListResponse<T> {
    data: T[]
    page: number
    total: number
    per_page: number
    current_page: number
  }

  // 与后端约定的响应数据格式
  interface ResponseStructure<T> {
    success: boolean
    data: T
    errorCode?: number
    msg?: string
    showType?: ErrorShowType
    status?: number
  }

  type TableData<T> = ResponseStructure<ListResponse<T>>

}

