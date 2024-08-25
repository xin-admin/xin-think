/**
 * @name proxy 代理的配置
 * @see 在生产环境 代理是无法生效的，所以这里没有生产环境的配置
 * -------------------------------
 * The agent cannot take effect in the production environment
 * so there is no configuration of the production environment
 * For details, please see
 * https://pro.ant.design/docs/deploy
 *
 * @doc https://umijs.org/docs/guides/proxy
 */
export default {
  // 如果需要自定义本地开发服务器  请取消注释按需调整
  dev: {
    '/admin.php': {
      target: 'http://127.0.0.1:8000/admin.php',
      changeOrigin: true,
      pathRewrite: { '^/admin.php': '' },
    },
    '/api.php': {
      target: 'http://127.0.0.1:8000/api.php',
      changeOrigin: true,
      pathRewrite: { '^/api.php': '' },
    },
  },
};
