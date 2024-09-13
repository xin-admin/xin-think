<?php

namespace app\common\library;

use Throwable;

class Terminal
{

    /**
     * @var resource|bool proc_open 返回的 resource
     */
    protected $process = false;


    private array $commands = [];

    private string $outputFile;

    private array $descriptorsPec;

    /**
     * @var array proc_open 的管道
     */
    protected array $pipes = [];

    /**
     * @var array proc执行状态数据
     */
    protected array $procStatusData = [];

    /**
     * @var int proc执行状态:0=未执行,1=执行中,2=执行完毕
     */
    protected int $procStatusMark = 0;

    /**
     * @var string 命令执行实时输出内容
     */
    protected string $outputContent = '';

    /**
     * @var string 错误内容
     */
    protected string $errorMessage = '';

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 初始化日志文件
        $outputDir        = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'terminal';
        $this->outputFile = $outputDir . DIRECTORY_SEPARATOR . 'exec.log';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        file_put_contents($this->outputFile, '');

        /**
         * 命令执行结果输出到文件而不是管道
         * 因为输出到管道时有延迟，而文件虽然需要频繁读取和对比内容，但是输出实时的
         */
        $this->descriptorsPec = [0 => ['pipe', 'r'], 1 => ['file', $this->outputFile, 'w'], 2 => ['file', $this->outputFile, 'w']];

        $this->commands = [
            'web-install' => [
                'command' => 'pnpm install',
                'cwd' => root_path() . '/web'
            ],
            'sql-install' => [
                'command' => 'php think migrate:run',
                'cwd' => root_path()
            ],
        ];
    }

    /**
     * 执行命令
     * @param string $commandKey 命令
     * @throws Throwable
     */
    public function exec(string $commandKey): bool
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        if (!ob_get_level()) ob_start();

        if(empty($this->commands[$commandKey])){
            $this->errorMessage = '命令不存在！';
            return false;
        }
        $command = $this->commands[$commandKey];
        if($command['command'] == 'pnpm install') {
            @unlink(root_path() . 'web' . DIRECTORY_SEPARATOR . 'pnpm-lock.yaml');
        }

        $this->process = proc_open($command['command'], $this->descriptorsPec, $this->pipes, $command['cwd']);
        if (!is_resource($this->process)) {
            $this->errorMessage = '进程创建错误！';
            return false;
        }
        while ($this->getProcStatus()) {
            $contents = file_get_contents($this->outputFile);
            if (strlen($contents) && $this->outputContent != $contents) {
                $newOutput = str_replace($this->outputContent, '', $contents);
                if (preg_match('/\r\n|\r|\n/', $newOutput)) {
                    $this->outputContent = $contents;
                }
            }

            if ($this->procStatusMark === 2) {
                if ($this->procStatusData['exitcode'] !== 0) {
                    $this->errorMessage = '任务执行失败！';
                    return false;
                }
            }

            usleep(500000);
        }
        foreach ($this->pipes as $pipe) {
            fclose($pipe);
        }
        proc_close($this->process);
        return true;
    }

    /**
     * 获取执行状态
     * @throws Throwable
     */
    public function getProcStatus(): bool
    {
        $this->procStatusData = proc_get_status($this->process);
        if ($this->procStatusData['running']) {
            $this->procStatusMark = 1;
            return true;
        } elseif ($this->procStatusMark === 1) {
            $this->procStatusMark = 2;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }



}