// https://umijs.org/config/
import { defineConfig } from '@umijs/max';
import proxy from './proxy';
import HtmlWebpackPlugin from 'html-webpack-plugin';
const { REACT_APP_ENV = 'dev' } = process.env;

export default defineConfig({
  /**
   * @name hash 开启 hash 模式
   * @description 让 build 之后的产物包含 hash 后缀。通常用于增量发布和避免浏览器加载缓存。
   * @doc https://umijs.org/docs/api/config#hash
   */
  hash: true,
  /**
   * @name theme 主题的配置
   * @description 虽然叫主题，但是其实只是 less 的变量设置
   * @doc antd的主题设置 https://ant.design/docs/react/customize-theme-cn
   * @doc umi 的theme 配置 https://umijs.org/docs/api/config#theme
   */
  theme: {
    // 如果不想要 configProvide 动态设置主题需要把这个设置为 default
    // 只有设置为 variable， 才能使用 configProvide 动态设置主色调
    'root-entry-name': 'variable',
  },
  /**
   * @name moment 的国际化配置
   * @description 如果对国际化没有要求，打开之后能减少js的包大小
   * @doc https://umijs.org/docs/api/config#ignoremomentlocale
   */
  ignoreMomentLocale: true,
  /**
   * @name proxy 代理配置
   * @description 可以让你的本地服务器代理到你的服务器上，这样你就可以访问服务器的数据了
   * @see 要注意以下 代理只能在本地开发时使用，build 之后就无法使用了。
   * @doc 代理介绍 https://umijs.org/docs/guides/proxy
   * @doc 代理配置 https://umijs.org/docs/api/config#proxy
   */
  proxy: proxy[REACT_APP_ENV as keyof typeof proxy],
  /**
   * @name fastRefresh 快速热更新配置
   * @description 一个不错的热更新组件，更新时可以保留 state
   */
  fastRefresh: true,
  //============== 以下都是max的插件配置 ===============
  /**
   * @name model 数据流插件
   * @@doc https://umijs.org/docs/max/data-flow
   */
  model: {},
  /**
   * 一个全局的初始数据流，可以用它在插件之间共享数据
   * @description 可以用来存放一些全局的数据，比如用户信息，或者一些全局的状态，全局初始状态在整个 Umi 项目的最开始创建。
   * @doc https://umijs.org/docs/max/data-flow#%E5%85%A8%E5%B1%80%E5%88%9D%E5%A7%8B%E7%8A%B6%E6%80%81
   */
  initialState: {},
  /**
   * @name layout 插件
   * @doc https://umijs.org/docs/max/layout-menu
   */
  layout: { locale: true, },
  /**
   * @name moment2dayjs 插件
   * @description 将项目中的 moment 替换为 dayjs
   * @doc https://umijs.org/docs/max/moment2dayjs
   */
  moment2dayjs: {
    preset: 'antd',
    plugins: ['duration'],
  },
  /**
   * @name locale 国际化插件
   * @doc https://umijs.org/docs/max/i18n
   */
  locale: {
    // default zh-CN
    default: 'zh-CN',
    antd: true,
    // default true, when it is true, will use `navigator.language` overwrite default
    baseNavigator: true,
  },
  /**
   * @name antd 插件
   * @description 内置了 babel import 插件
   * @doc https://umijs.org/docs/max/antd#antd
   */
  antd: {},
  /**
   * @name request 网络请求配置
   * @description 它基于 axios 和 ahooks 的 useRequest 提供了一套统一的网络请求和错误处理方案。
   * @doc https://umijs.org/docs/max/request
   */
  request: {},
  /**
   * @name access 权限插件
   * @description 基于 initialState 的权限插件，必须先打开 initialState
   * @doc https://umijs.org/docs/max/access
   */
  access: {},
  /**
   * @name <head> 中额外的 script
   * @description 配置 <head> 中额外的 script
   */
  headScripts: [
    // 解决首次加载时白屏的问题
    { src: '/scripts/loading.js', async: true },
  ],
  mfsu: {
    strategy: 'normal',
  },
  // mako: {},
  /**
   * 约定式路由配置
   */
  conventionRoutes: {
    exclude: [/\/components\//, /\/models\//,/\/noLayoutPage\//,/\/Public\//],
    base: 'src/pages'
  },
  esbuildMinifyIIFE: true,
  npmClient: 'pnpm',
  /**
   * 第三方统计，百度统计配置
   */
  analytics: {
    baidu: '03fb97a161517001d21cf900d8d328df',
  },
  // publicPath: '/assets/',
  // // 输出目录
  // outputPath: './dist/assets',
  // base: '/dict/',
  // // 自定义 webpack 配置
  // chainWebpack(config) {
  //   config.plugin('html').use(HtmlWebpackPlugin, [{
  //     filename: '../index.html', // 将 index.html 文件输出到 /dict/index.html
  //   }]);
  // },
  favicons: [
    'https://file.xinadmin.cn/file/favicons.ico'
  ],
  metas: [
    { name: 'keywords', content: 'Xin Admin,Umi,Umi js,中后台管理框架,React,ThinkPHP,xinadmin,admin,react admin,think admin' },
    { name: 'description', content: 'Xin Admin是一款基于 Ant Design Pro components 构建一套完善的 Xin Table， 只需一个 Columns 就可以实现增删改查等表单、表格、查询等功能，以及组件的高度自定义' },
  ],
  plugins: [
    './config/plugins/multiRoutes'
  ],
});
