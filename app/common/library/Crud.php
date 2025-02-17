<?php
// +----------------------------------------------------------------------
// | XinAdmin [ A Full stack framework ]
// +----------------------------------------------------------------------
// | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小刘同学 <2302563948@qq.com>
// +----------------------------------------------------------------------
namespace app\common\library;

use app\admin\model\admin\AdminRuleModel as AdminRuleModel;
use Exception;
use think\facade\Db;
use think\facade\View;

class Crud
{
    private array $columns;
    private array $tableConfig;
    private array $crudConfig;
    private array $viewData;
    private string $errorMsg;

    public function __construct(array $data)
    {
        $this->columns = $data['columns'];
        $this->tableConfig = $data['table_config'];
        $this->crudConfig = $data['crud_config'];
        $this->viewData = [
            'controllerPath' => str_replace('/', '\\', $this->crudConfig['controllerPath']),
            'modelPath' => str_replace('/', '\\', $this->crudConfig['modelPath']),
            'validatePath' => str_replace('/', '\\', $this->crudConfig['validatePath']),
            'name' => $this->crudConfig['name']
        ];
    }

    /**
     * crud
     * @return bool
     */
    public function generate(): bool
    {
        try {
            $this->buildSql();
            $this->buildController();
            $this->buildModel();
            $this->buildValidate();
            $this->buildPage();
            $this->saveAuth();
            return true;
        }catch (Exception $exception ) {
            $this->setErrorMsg($exception->getMessage());
            return false;
        }
    }

    /**
     * 构建 sql
     * @return void
     * @throws Exception
     */
    private function buildSql(): void
    {
        $crudConfig = $this->crudConfig;
        if(!isset($crudConfig['sqlTableName']) || !$crudConfig['sqlTableName']) {
            throw new Exception('请输入数据表名称');
        }
        $tableName = env('DB_PREFIX', 'xin_') . $crudConfig['sqlTableName'];
        $sql  = "CREATE TABLE IF NOT EXISTS `{$tableName}` (" . PHP_EOL;
        $pk   = '';
        foreach ($this->columns as $field) {
            // 数据库 sql 配置
            if (!isset($field['sqlType']) || !$field['sqlType']) {
                continue;
            }
            $unsigned      = (isset($field['unsign']) && $field['unsign']) ? ' UNSIGNED' : '';
            $null          = (isset($field['null']) && $field['null']) ? ' NOT NULL' : '' ;
            $autoIncrement = (isset($field['autoIncrement']) && $field['autoIncrement']) ? ' AUTO_INCREMENT' : '';
            $length        = (isset($field['sqlLength']) && $field['sqlLength']) ? "({$field['sqlLength']})": '';
            $default       = '';
            if ($field['defaultValue'] !== '') {
                $default = " DEFAULT '{$field['defaultValue']}'";
            }
            if (strtolower((string)$field['defaultValue']) == 'null') {
                $default = ' DEFAULT NULL';
            }
            if (strtolower((string)$field['defaultValue']) == 'empty string') {
                $default = " DEFAULT ''";
            }
            if ($field['defaultValue'] == '0') {
                $default = " DEFAULT 0";
            }
            $fieldComment = (isset($field['remark']) && $field['remark']) ? " COMMENT '{$field['remark']}'" : '';
            $sql          .= "`{$field['dataIndex']}` {$field['sqlType']}$length$unsigned$null$autoIncrement$default$fieldComment ," . PHP_EOL;
            if (isset($field['isKey']) && $field['isKey']) {
                $pk = $field['dataIndex'];
            }
        }
        if(isset($crudConfig['autoDeletetime']) && $crudConfig['autoDeletetime']){
            $sql .= "`delete_time` INT(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',";
        }
        $sql .= "PRIMARY KEY (`$pk`)" . PHP_EOL . ") ";
        $comment = $crudConfig['sqlTableRemark']??'';
        $sql .= "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='{$comment}'";
        Db::execute($sql);
    }

    /**
     * 构建控制器
     * @return void
     */
    private function buildController(): void
    {
        $select = '';
        foreach ($this->columns as $item){
            if(!isset($item['hideInSearch']) || !$item['hideInSearch']){
                if(isset($item['select'])){
                    $select .= "        '{$item['dataIndex']}'=> '{$item['select']}',".PHP_EOL;
                }else {
                    $select .= "        '{$item['dataIndex']}'=> '=',".PHP_EOL;
                }
            }
        }
        $viewData = $this->viewData;
        $viewData['select'] = $select;
        $path = $this->crudConfig['pagePath'].'/'.$this->crudConfig['name'];
        $auth = preg_replace('/\//', '.', $path);
        $viewData['authName'] = ltrim($auth, '.');
        // 控制器渲染
        $controllerView = View::fetch('crud/controller',$viewData);
        $path = root_path() . '/app/admin/controller/' . $this->crudConfig['controllerPath'] . '/';
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        file_put_contents($path.$this->crudConfig['name'].'Controller.php', $controllerView);
    }

    /**
     * 构建模型
     * @return void
     */
    private function buildModel(): void
    {
        // 模型渲染
        $viewData = $this->viewData;
        $viewData['autoDeletetime'] = $this->crudConfig['autoDeletetime'];
        $viewData['tableName'] = 'xin_'.$this->crudConfig['sqlTableName'];
        $modelView = View::fetch('crud/model',$viewData);

        $path = root_path() . '/app/admin/model/' . $this->crudConfig['modelPath'] . '/' ;
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        file_put_contents($path.$this->crudConfig['name'].'Model.php', $modelView);
    }

    /**
     * 构建验证器
     * @return void
     */
    private function buildValidate(): void
    {
        $validation = '';
        $massage = '';
        foreach ($this->columns as $item){
            if(isset($item['validation']) && $item['validation']){
                $validation .= "        '{$item['dataIndex']}'  => '";

                foreach ($item['validation'] as $val){
                    switch ($val){
                        case 'verifyRequired':
                            $validation .= 'require|';
                            $massage .= "        '{$item['dataIndex']}.require' => '字段{$item['title']}必填'," . PHP_EOL;
                            break;
                        case 'verifyEmail':
                            $validation .= 'email|';
                            $massage .= "        '{$item['dataIndex']}.email' => '{$item['title']}必须为 email'," . PHP_EOL;
                            break;
                        case 'verifyUrl':
                            $validation .= 'url|';
                            $massage .= "        '{$item['dataIndex']}.url' => '{$item['title']}必须为URL'," . PHP_EOL;
                            break;
                        case 'verifyNumber':
                            $validation .= 'number|';
                            $massage .= "        '{$item['dataIndex']}.number' => '{$item['title']}必须为数字'," . PHP_EOL;
                            break;
                        case 'verifyString':
                            $validation .= 'string|';
                            $massage .= "        '{$item['dataIndex']}.string' => '{$item['title']}必须为字符串'," . PHP_EOL;
                            break;
                        case 'verifyInteger':
                            $validation .= 'integer|';
                            $massage .= "        '{$item['dataIndex']}.integer' => '{$item['title']}必须为纯数字'," . PHP_EOL;
                            break;
                        case 'verifyMobile':
                            $validation .= 'mobile|';
                            $massage .= "        '{$item['dataIndex']}.mobile' => '{$item['title']}必须为手机号'," . PHP_EOL;
                            break;
                        case 'verifyIdCard':
                            $validation .= 'idCard|';
                            $massage .= "        '{$item['dataIndex']}.idCard' => '{$item['title']}必须为身份证号'," . PHP_EOL;
                            break;
                    }
                }
                $validation = substr($validation, 0, -1);
                $validation .= "',".PHP_EOL;
            }
        }
        $viewData = $this->viewData;
        $viewData['validation'] = $validation;
        $viewData['massage']    = $massage;

        // 验证器渲染
        $validateView = View::fetch('crud/validate',$viewData);
        $path = root_path(). '/app/admin/validate/' . $this->crudConfig['validatePath'].'/';
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        file_put_contents($path.$this->crudConfig['name'].'.php', $validateView);
    }


    /**
     * 构建前端页面
     * @return void
     */
    private function buildPage(): void
    {
        $columns = '['.PHP_EOL;
        foreach ($this->columns as $field) {
            if(!isset($field['valueType']) && !$field['valueType']){
                continue;
            }
            $columnsData = '    {' . PHP_EOL;
            $columnsData .= "      valueType:'{$field['valueType']}'," . PHP_EOL;
            $columnsData .= "      dataIndex:'{$field['dataIndex']}'," . PHP_EOL;
            if($field['valueType'] === 'color') {
                $columnsData .= "      transform: (value) => {" . PHP_EOL;
                $columnsData .= "        return { {$field['dataIndex']}: typeof value === 'string' ? value :  value.toHexString() }" . PHP_EOL;
                $columnsData .= "      }," . PHP_EOL;
            }
            if(in_array($field['valueType'],['select','checkbox','radio','radioButton'])) {
                $columnsData .= "      valueEnum: new Map([".PHP_EOL;
                $enum = explode("\n",$field['enum']);
                foreach($enum as $item){
                    $l = explode(':',$item);
                    $columnsData .= "        [{$l[0]},'{$l[1]}'],".PHP_EOL;
                }
                $columnsData .= "      ]),".PHP_EOL;
            }

            if(isset($field['title']) && $field['title']){
                $columnsData .= "      title:'{$field['title']}'," . PHP_EOL;
            }
            if(isset($field['hideInSearch']) && $field['hideInSearch']){
                $columnsData .= '      hideInSearch: true,' . PHP_EOL;
            }
            if(isset($field['hideInTable']) && $field['hideInTable']){
                $columnsData .= '      hideInTable: true,' . PHP_EOL;
            }
            if(isset($field['hideInForm']) && $field['hideInForm']){
                $columnsData .= '      hideInForm: true,' . PHP_EOL;
            }
            if(isset($field['validation']) && $field['validation']){
                $val = '';
                foreach ($field['validation'] as $item){
                    $val .= "          verify.{$item},".PHP_EOL;
                }
                $columnsData .= "      formItemProps: {".PHP_EOL."        rules: [".PHP_EOL . $val .'        ]'.PHP_EOL."      },".PHP_EOL;
            }

            $columnsData .= '    },'.PHP_EOL;
            $columns .= $columnsData;
        }
        $columns .= '  ]';


        $tableConfig = '{'.PHP_EOL;
        $tableConfig .= '    tableApi: api,'.PHP_EOL;
        $tableConfig .= '    columns: columns,'.PHP_EOL;
        $path = $this->crudConfig['pagePath'].'/'.$this->crudConfig['name'];
        $auth = preg_replace('/\//', '.', $path);
        $auth = ltrim($auth, '.');
        $tableConfig .= "    accessName: '".$auth."',".PHP_EOL;
        foreach ($this->tableConfig as $key=>$item) {
            if(is_array($item)) {
                $tableConfig .= "    {$key}: {" . PHP_EOL;
                foreach ($item as $key2 => $item2) {
                    if($item2 === false) {
                        $tableConfig .= "      {$key2}: false," . PHP_EOL;
                        continue;
                    }
                    if($key2 == 'collapseRender' && $item2 === true) {
                        continue;
                    }
                    if($item2 === true) {
                        $tableConfig .= "      {$key2}: true," . PHP_EOL;
                        continue;
                    }
                    if($key2 == 'span') {
                        $tableConfig .= "      {$key2}: $item2," . PHP_EOL;
                        continue;
                    }
                    $tableConfig .= "      {$key2}:'{$item2}'," . PHP_EOL;
                }
                $tableConfig .= "    }," . PHP_EOL;
                continue;
            }
            if($item === false) {
                $tableConfig .= "    {$key}: false," . PHP_EOL;
                continue;
            }
            if($item === true) {
                $tableConfig .= "    {$key}: true," . PHP_EOL;
                continue;
            }
            $tableConfig .= "    {$key}: '{$item}'," . PHP_EOL;
        }
        $tableConfig .= '  }' . PHP_EOL;
        // 提取控制器名称（去除路径和文件扩展名）
        $controllerPath = explode('controller',$this->crudConfig['controllerPath']);
        if(isset($controllerPath[1]) && $controllerPath[1] != '') {
            $apiPath = explode('/',$controllerPath[1]);
            array_shift($apiPath);
            $api = implode('.',$apiPath) .'.'. $this->crudConfig['name'];
        }else {
            $api = $this->crudConfig['name'];
        }

        // 视图渲染
        $pageData = [
            'columns' => $columns,
            'name'    => $this->crudConfig['name'],
            'table_config' => $tableConfig,
            'api'     => $api
        ];
        $web_path = env('web_path', './web');
        $pagePath = root_path() . $web_path .'/src/pages/backend' . $this->crudConfig['pagePath'] . '/' . $this->crudConfig['name'];
        if(!is_dir($pagePath)){
            mkdir($pagePath, 0777, true); // 使用递归创建目录
        }
        $modelView = View::fetch('crud/pages',$pageData);
        file_put_contents($pagePath.'/index.tsx', $modelView);
    }


    /**
     * @return void
     * @throws Exception
     */
    private function saveAuth(): void
    {
        $path = $this->crudConfig['pagePath'].'/'.$this->crudConfig['name'];
        $auth = preg_replace('/\//', '.', $path);
        $auth = ltrim($auth, '.');
        $data = [
            'icon' => $this->crudConfig['menuIcon']??'',
            'name' => $this->crudConfig['sqlTableRemark'],
            'path' => $path,
            'sort' => 20,
            'type' => 0,
            'pid' => 0,
            'key' => $auth,
            'remark' => $this->crudConfig['sqlTableRemark']??''
        ];
        $model = new AdminRuleModel();
        $model->save($data);
        $dataAll = [
            [
                'name' => $this->crudConfig['sqlTableRemark'] . '新增',
                'sort' => 0,
                'type' => 2,
                'pid' => $model->getKey(),
                'key' => $auth . '.add',
                'create_time' => time(),
                'update_time' => time()
            ],
            [
                'name' => $this->crudConfig['sqlTableRemark'] . '编辑',
                'sort' => 0,
                'type' => 2,
                'pid' => $model->getKey(),
                'key' => $auth . '.edit',
                'create_time' => time(),
                'update_time' => time()
            ],
            [
                'name' => $this->crudConfig['sqlTableRemark'] . '查询',
                'sort' => 0,
                'type' => 2,
                'pid' => $model->getKey(),
                'key' => $auth . '.list',
                'create_time' => time(),
                'update_time' => time()
            ],
            [
                'name' => $this->crudConfig['sqlTableRemark'] . '删除',
                'sort' => 0,
                'type' => 2,
                'pid' => $model->getKey(),
                'key' => $auth . '.delete',
                'create_time' => time(),
                'update_time' => time()
            ]
        ];
        $model->saveAll($dataAll);
    }

    /**
     * @param string $msg
     * @return void
     */
    private function setErrorMsg(string $msg): void
    {
        $this->errorMsg = $msg;
    }

    /**
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }
}