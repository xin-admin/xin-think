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
namespace app\api\controller;

use app\api\validate\Install;
use app\BaseController;
use think\db\exception\PDOException;
use think\facade\Config;
use think\facade\Db;
use think\response\Json;

class InstallController extends BaseController
{
    /**
     * 需要的依赖版本
     */
    static array $needVersion = [
        'php'  => '8.0.2',
        'node' => '18.18.2',
        'pnpm' => '6.32.13',
    ];

    protected function initialize(): void
    {

    }

    /**
     * 环境检查
     * @return Json
     */
    public function baseCheck(): Json
    {

        $checkData = [];

        // php版本-start
        $phpVersion        = phpversion();
        $phpVersionCompare = static::compareVersion(static::$needVersion['php'], $phpVersion);
        $checkData[] = [
            'name' => 'PHP',
            'status' => $phpVersionCompare,
            'message' => $phpVersionCompare ? $phpVersion : '需要' . ' >= ' . static::$needVersion['php']
        ];
        // php版本-end

        // 配置文件-start
        $dbConfigFile     = config_path() . 'database.php';
        $configIsWritable = static::pathIsWritable(config_path()) && static::pathIsWritable($dbConfigFile);
        $checkData[] = [
            'name' => 'config 目录',
            'status' => $configIsWritable,
            'message' => $configIsWritable ? '可写' : '配置文件不可写'
        ];
        // 配置文件-end

        // public-start
        $publicIsWritable = static::pathIsWritable(public_path());
        $checkData[] = [
            'name' => 'public 目录',
            'status' => $publicIsWritable,
            'message' => $publicIsWritable ? '可写' : '配置文件不可写'
        ];
        // public-end

        // PDO-start
        $phpPdo = extension_loaded("PDO");
        $checkData[] = [
            'name' => 'PDO ' . 'extensions',
            'status' => $phpPdo,
            'message' => $phpPdo ? '已安装' : 'PDO扩展未安装'
        ];
        // PDO-end

        // proc_open
        $phpProc = function_exists('proc_open') && function_exists('proc_close') && function_exists('proc_get_status');
        $checkData[] = [
            'name' => 'proc_open ' . '函数状态',
            'status' => $phpProc,
            'message' => $phpProc ? '可用' : '请移除 proc_open 函数禁用'
        ];
        $phpProc = function_exists('proc_close');
        $checkData[] = [
            'name' => 'proc_close ' . '函数状态',
            'status' => $phpProc,
            'message' => $phpProc ? '可用' : '请移除 proc_close 函数禁用'
        ];
        $phpProc = function_exists('proc_get_status');
        $checkData[] = [
            'name' => 'proc_get_status ' . '函数状态',
            'status' => $phpProc,
            'message' => $phpProc ? '可用' : '请移除 proc_get_status 函数禁用'
        ];
        // proc_open-end

        // node
        $nodeVersion        = static::getVersion('node');
        $nodeVersionCompare = static::compareVersion(static::$needVersion['node'], $nodeVersion);
        $checkData[] = [
            'name' => 'nodejs',
            'status' => $nodeVersion && $nodeVersionCompare,
            'message' => $nodeVersionCompare ? $nodeVersion : 'node 未安装 或 node 版本过低'
        ];
        // node-end

        // pnpm
        $pnpmVersion        = static::getVersion('pnpm');
        $pnpmVersionCompare = static::compareVersion(static::$needVersion['pnpm'], $pnpmVersion);
        $checkData[] = [
            'name' => 'pnpm',
            'status' => $pnpmVersion && $pnpmVersionCompare,
            'message' => $pnpmVersionCompare ? $pnpmVersion : 'pnpm 未安装 或 pnpm 版本过低'
        ];
        // pnpm-end

        return $this->success($checkData);
    }

    /**
     * 测试数据库连接
     */
    public function testDatabase(): Json
    {
        $database = [
            'hostname' => $this->request->post('mysql_hostname', '127.0.0.1'),
            'username' => $this->request->post('mysql_username', 'root'),
            'password' => $this->request->post('mysql_password', 'root'),
            'port' => $this->request->post('mysql_port', '3306'),
            'name' => $this->request->post('mysql_name', 'xin-admin'),
        ];
        try {
            $dbConfig                         = Config::get('database');
            $dbConfig['connections']['mysql'] = array_merge($dbConfig['connections']['mysql'], $database);
            Config::set(['connections' => $dbConfig['connections']], 'database');

            $connect = Db::connect('mysql');
            $connect->execute("SELECT 1");

            $tables = $connect->query("SHOW DATABASES");
            trace($tables);
            if (!in_array($database['name'], array_column($tables, 'Database'))) {
                return $this->error('数据库不存在，请先创建数据库！');
            }
            return $this->success('ok');
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            return $this->error($errorMsg);
        }
    }

    /**
     * 写入 Env
     * @return Json
     */
    public function writeEnv(): Json
    {
        $data = $this->request->post();
        $val = new Install();
        if(!$val->check($data)) {
            return $this->error($val->getError());
        }
        // 写入.env文件
        $envFile         = root_path() . '.env';
        $envFileContent  = '[DATABASE]' . "\n";
        $envFileContent .= 'DB_TYPE = mysql' . "\n";
        $envFileContent .= 'DB_HOST = ' . $data['mysql_hostname'] . "\n";
        $envFileContent .= 'DB_NAME = ' . $data['mysql_name'] . "\n";
        $envFileContent .= 'DB_USER = ' . $data['mysql_username'] . "\n";
        $envFileContent .= 'DB_PASS = ' . $data['mysql_password'] . "\n";
        $envFileContent .= 'DB_PORT = ' . $data['mysql_port'] . "\n";
        $envFileContent .= 'DB_PREFIX = ' . $data['mysql_prefix'] . "\n";
        $envFileContent .= 'DB_CHARSET = utf8mb4' . "\n";
        $envFileContent .= "\n" . '[DEBUG]' . "\n";
        $envFileContent .= 'APP_DEBUG = true' . "\n";
        $envFileContent .= "\n" . '[WEB]' . "\n";
        $envFileContent .= 'WEB_PATH = ./web' . "\n";
        $result         = @file_put_contents($envFile, $envFileContent);
        if (!$result) {
            return $this->error('文件不可写');
        }
        return $this->success('ok');
    }

    /**
     * 安装数据库
     * @return Json
     */
    public function installDb(): Json
    {
        $dbInstall = self::getOutputFromProc('');
    }


    /**
     * 比较两个版本号
     * @param $v1 string 要求的版本号
     * @param $v2 bool | string 被比较版本号
     * @return bool 是否达到要求的版本号
     */
    private static function compareVersion(string $v1, bool|string $v2): bool
    {
        if (!$v2) {
            return false;
        }

        // 删除开头的 V
        if (strtolower($v1[0]) == 'v') {
            $v1 = substr($v1, 1);
        }
        if (strtolower($v2[0]) == 'v') {
            $v2 = substr($v2, 1);
        }

        if ($v1 == "*" || $v1 == $v2) {
            return true;
        }

        // 丢弃'-'后面的内容
        if (str_contains($v1, '-')) $v1 = explode('-', $v1)[0];
        if (str_contains($v2, '-')) $v2 = explode('-', $v2)[0];

        $v1 = explode('.', $v1);
        $v2 = explode('.', $v2);

        // 将号码逐个进行比较
        for ($i = 0; $i < count($v1); $i++) {
            if (!isset($v2[$i])) {
                break;
            }
            if ($v1[$i] == $v2[$i]) {
                continue;
            }
            if ($v1[$i] > $v2[$i]) {
                return false;
            }
            if ($v1[$i] < $v2[$i]) {
                return true;
            }
        }
        if (count($v1) != count($v2)) {
            return !(count($v1) > count($v2));
        }
        return false;
    }

    /**
     * 检查目录/文件是否可写
     * @param $path
     * @return bool
     */
    private static function pathIsWritable($path): bool
    {
        if (DIRECTORY_SEPARATOR == '/' && !@ini_get('safe_mode')) {
            return is_writable($path);
        }

        if (is_dir($path)) {
            $path = rtrim($path, '/') . '/' . md5(mt_rand(1, 100) . mt_rand(1, 100));
            if (($fp = @fopen($path, 'ab')) === false) {
                return false;
            }

            fclose($fp);
            @chmod($path, 0777);
            @unlink($path);

            return true;
        } elseif (!is_file($path) || ($fp = @fopen($path, 'ab')) === false) {
            return false;
        }

        fclose($fp);
        return true;
    }

    /**
     * 获取前端依赖版本号
     * @param string $name
     * @return string
     */
    private static function getVersion(string $name): string
    {
        if($name == 'npm') {
            $command = 'npm -v';
        }else if($name == 'node') {
            $command = 'node -v';
        }else if($name == 'pnpm') {
            $command = 'pnpm -v';
        }else {
            return '';
        }
        $execOut = static::getOutputFromProc($command);
        if (!$execOut) return '';
        if (strripos($execOut, 'npm WARN') !== false) {
            $preg = '/\d+(\.\d+){0,2}/';
            preg_match($preg, $execOut, $matches);
            if (isset($matches[0]) && static::checkDigitalVersion($matches[0])) {
                return $matches[0];
            }
        }
        $execOut = preg_split('/\r\n|\r|\n/', $execOut);
        // 检测两行，第一行可能会是个警告消息
        for ($i = 0; $i < 2; $i++) {
            if (isset($execOut[$i]) && static::checkDigitalVersion($execOut[$i])) {
                return $execOut[$i];
            }
        }
        return '';
    }

    /**
     * 是否是一个数字版本号
     * @param $version
     * @return bool
     */
    private static function checkDigitalVersion($version): bool
    {
        if (!$version) {
            return false;
        }
        if (strtolower($version[0]) == 'v') {
            $version = substr($version, 1);
        }

        $rule1 = '/\.{2,10}/'; // 是否有两个的`.`
        $rule2 = '/^\d+(\.\d+){0,10}$/';
        if (!preg_match($rule1, (string)$version)) {
            return !!preg_match($rule2, (string)$version);
        }
        return false;
    }

    /**
     * 执行一个命令并以字符串的方式返回执行输出
     * 代替 exec 使用，这样就只需要解除 proc_open 的函数禁用了
     * @param $command
     * @return string
     */
    private static function getOutputFromProc($command): string
    {
        if (!function_exists('proc_open') || !function_exists('proc_close')) {
            return false;
        }
        $descriptorsPec = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process        = proc_open($command, $descriptorsPec, $pipes, null, null);
        if (is_resource($process)) {
            $info = stream_get_contents($pipes[1]);
            $info .= stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return self::outputFilter($info);
        }
        return '';
    }

    /**
     * 输出过滤
     */
    private static function outputFilter($str): string
    {
        $str  = trim($str);
        $preg = '/\[(.*?)m/i';
        $str  = preg_replace($preg, '', $str);
        $str  = str_replace(["\r\n", "\r", "\n"], "\n", $str);
        return mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
    }



}