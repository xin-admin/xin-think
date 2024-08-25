declare namespace OnlineType {
  type TableConfig = {
    // 表格设置
    headerTitle: string; // 表格标题
    tooltip: string; // 表格提示
    size: 'small' | 'middle' | 'large' | undefined; // 表格大小
    // 功能开关
    addShow: boolean; // 新增显示
    editShow: boolean; // 编辑显示
    deleteShow: boolean; // 删除显示
    rowSelectionShow: boolean; // 多选显示
    bordered: boolean; // 边框显示
    showHeader: boolean; // 表头显示
    // 查询配置
    searchShow?: boolean; // 查询开关
    search: {
      resetText: string; // 重置按钮文案
      searchText: string; // 查询按钮文案
      span: number; // 表单栅格
      layout: 'vertical' | 'horizontal'; // 表单布局
      filterType: 'query' | 'light'; // 表单类型
    } | false; // 表格搜索
    // 操作栏配置
    optionsShow?: boolean; // 操作栏开关
    options: {
      density: boolean; // 密度按钮
      search: boolean; // 一键搜索
      fullScreen: boolean; // 全屏按钮
      setting: boolean; // 列设置
      reload: boolean; // 刷新按钮
    } | false;
    // 分页配置
    paginationShow?: boolean;
    pagination: {
      size: 'default' | 'small'; // 分页大小
      simple?: boolean; // 简洁分页
    } | false;
  }


  interface ColumnsConfig {
    key?: string;
    valueType?: string;
    dataIndex?: string;
    title?: string;
    // 生成设置
    select?: string | false;
    validation?: string[];
    hideInSearch?: boolean;
    hideInTable?: boolean;
    hideInForm?: boolean;
    enum?: string;
    // 数据库配置
    defaultValue?: string;
    sqlType?: string;
    sqlLength?: number;
    isKey?: boolean;
    null?: boolean;
    autoIncrement?: boolean;
    unsign?: boolean;
    mock?: string;
  }

  interface CrudConfig {
    name?: string
    controllerPath?: string
    modelPath?: string
    validatePath?: string
    pagePath?: string
    sqlTableName?: string
    sqlTableRemark?: string
    autoDeletetime?: boolean
  }

}
