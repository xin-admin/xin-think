<?php

use think\migration\Migrator;

class Install extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->admin();
        $this->adminGroup();
        $this->adminRule();
        $this->dict();
        $this->dictItem();
        $this->file();
        $this->fileGroup();
        $this->token();
        $this->onlineTable();
        $this->settingGroup();
        $this->setting();
        $this->user();
        $this->userGroup();
        $this->userRule();
        $this->userMoneyLog();
        $this->verificationCode();
    }

    public function admin(): void
    {
        if (!$this->hasTable('admin')) {
            $table = $this->table('admin', [
                'id' => false,
                'comment' => '管理员表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('username', 'string', ['limit' => 30, 'default' => '', 'comment' => '用户名', 'null' => false])
                ->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户昵称', 'null' => false])
                ->addColumn('avatar_id', 'integer', ['comment' => '头像', 'null' => false])
                ->addColumn('sex', 'string', ['limit' => 3, 'comment' => '性别', 'null' => false, 'default' => '0'])
                ->addColumn('email', 'string', ['limit' => 50, 'comment' => '邮箱', 'null' => false, 'default' => ''])
                ->addColumn('mobile', 'string', ['limit' => 11, 'comment' => '手机号', 'null' => false, 'default' => ''])
                ->addColumn('status', 'string', ['limit' => 1, 'comment' => '状态:0=禁用,1=启用', 'null' => false, 'default' => '1'])
                ->addColumn('group_id', 'integer', ['comment' => '分组ID', 'null' => false, 'default' => 0])
                ->addColumn('password', 'string', ['limit' => 60, 'comment' => '密码', 'null' => false, 'default' => ''])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex(['username'], ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function adminGroup(): void
    {
        if (!$this->hasTable('admin_group')) {
            $table = $this->table('admin_group', [
                'id' => false,
                'comment' => '管理分组表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('pid', 'integer', ['comment' => '上级分组ID', 'default' => 0, 'signed' => false, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '分组名称', 'null' => false])
                ->addColumn('rules', 'text', ['null' => true, 'default' => null, 'comment' => '权限ID'])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

    public function adminRule(): void
    {
        if (!$this->hasTable('admin_rule')) {
            $table = $this->table('admin_rule', [
                'id' => false,
                'comment' => '管理员权限规则表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('pid', 'integer', ['comment' => '父ID', 'null' => false, 'default' => 0, 'limit' => 10])
                ->addColumn('type', 'string', ['comment' => '类型 0：页面 1：数据 2：按钮', 'null' => false, 'default' => 0, 'limit' => 1])
                ->addColumn('sort', 'integer', ['comment' => '排序', 'null' => false, 'default' => 0])
                ->addColumn('name', 'string', ['comment' => '标题', 'null' => false, 'default' => '', 'limit' => 50])
                ->addColumn('path', 'string', ['comment' => '路由地址', 'null' => true, 'default' => '', 'limit' => 50])
                ->addColumn('icon', 'string', ['comment' => '图标', 'null' => true, 'default' => '', 'limit' => 255])
                ->addColumn('key', 'string', ['comment' => '权限标识', 'null' => false, 'limit' => 50])
                ->addColumn('locale', 'string', ['comment' => '国际化标识', 'null' => true, 'default' => '', 'limit' => 50])
                ->addColumn('status', 'integer', ['comment' => '启用状态', 'null' => false, 'default' => 1, 'limit' => 1])
                ->addColumn('show', 'integer', ['comment' => '显示状态', 'null' => false, 'default' => 1, 'limit' => 1])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex(['key'], ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function dict(): void
    {
        if(!$this->hasTable('dict')) {
            $table = $this->table('dict', [
                'id' => false,
                'comment' => '数据字典表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('name', 'string', ['comment' => '字典名', 'null' => false, 'limit' => 20])
                ->addColumn('type', 'string', ['comment' => '类型', 'null' => false, 'default' => 'default', 'limit' => 10])
                ->addColumn('describe', 'string', ['comment' => '字典描述', 'null' => false, 'default' => '', 'limit' => 300])
                ->addColumn('code', 'string', ['comment' => '字典编码', 'null' => false, 'default' => '', 'limit' => 30])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('code', ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function dictItem(): void
    {
        if(!$this->hasTable('dict_item')) {
            $table = $this->table('dict_item', [
                'id' => false,
                'comment' => '字典项列表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('dict_id', 'integer', ['comment' => '字典ID', 'null' => false, 'signed' => false])
                ->addColumn('label', 'string', ['comment' => '字典项名称', 'null' => false, 'limit' => 30])
                ->addColumn('value', 'string', ['comment' => '数据值', 'null' => false, 'limit' => 30])
                ->addColumn('switch', 'string', ['comment' => '是否启用：0：禁用，1：启用', 'null' => false, 'limit' => 1, 'default' => '1'])
                ->addColumn('status', 'string', ['comment' => '状态：（default,success,error,processing,warning）', 'null' => false, 'limit' => 10, 'default' => 'default'])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

    public function file(): void
    {
        if(!$this->hasTable('file')) {
            $table = $this->table('file', [
                'id' => false,
                'comment' => '文件库记录表',
                'primary_key' => 'file_id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('file_id', 'integer', ['comment' => '文件ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('group_id', 'integer', ['comment' => '文件分组ID', 'null' => false, 'default' => 0, 'limit' => 10, 'signed' => false])
                ->addColumn('channel', 'integer', ['comment' => '上传来源(10商户后台 20用户端)', 'null' => false, 'default' => 10, 'limit' => 2, 'signed' => false])
                ->addColumn('storage', 'string', ['comment' => '存储方式', 'null' => false, 'default' => '', 'limit' => 10])
                ->addColumn('domain', 'string', ['comment' => '存储域名', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('file_type', 'integer', ['comment' => '文件类型(10图片 20附件 30视频)', 'null' => false, 'default' => 10, 'limit' => 10, 'signed' => false])
                ->addColumn('file_name', 'string', ['comment' => '文件名称(仅显示)', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('file_path', 'string', ['comment' => '文件路径', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('file_size', 'integer', ['comment' => '文件大小(字节)', 'null' => false, 'default' => 0, 'limit' => 10])
                ->addColumn('file_ext', 'string', ['comment' => '文件扩展名', 'null' => false, 'default' => '', 'limit' => 20])
                ->addColumn('cover', 'string', ['comment' => '文件封面', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('uploader_id', 'integer', ['comment' => '上传者用户ID', 'null' => false, 'default' => 0, 'limit' => 10])
                ->addColumn('is_recycle', 'integer', ['comment' => '是否在回收站', 'null' => false, 'default' => 0, 'limit' => 1])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex(['group_id'], ['type' => 'btree'])
                ->addIndex(['is_recycle'], ['type' => 'btree'])
                ->create();
        }
    }

    public function fileGroup(): void
    {
        if(!$this->hasTable('file_group')) {
            $table = $this->table('file_group', [
                'id' => false,
                'comment' => '文件库分组记录表',
                'primary_key' => 'group_id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('group_id', 'integer', ['comment' => '分组ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('name', 'string', ['comment' => '分组名称', 'null' => false, 'default' => '', 'limit' => 30])
                ->addColumn('parent_id', 'integer', ['comment' => '上级分组ID', 'null' => false, 'default' => 0, 'limit' => 10, 'signed' => false])
                ->addColumn('sort', 'integer', ['comment' => '排序(数字越小越靠前)', 'null' => false, 'default' => 0, 'limit' => 10, 'signed' => false])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

    public function onlineTable(): void
    {
        if(!$this->hasTable('online_table')) {
            $table = $this->table('online_table', [
                'id' => false,
                'comment' => '在线开发记录表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => '在线开发ID', 'null' => false, 'signed' => false, 'identity' => true])
                ->addColumn('table_name', 'string', ['comment' => '表格名', 'null' => false, 'default' => '', 'limit' => 30])
                ->addColumn('columns', 'text', ['comment' => '表头Json', 'null' => true])
                ->addColumn('crud_config', 'text', ['comment' => 'crud配置', 'null' => true])
                ->addColumn('table_config', 'text', ['comment' => '基础配置', 'null' => true])
                ->addColumn('describe', 'string', ['comment' => '描述', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

    public function setting(): void
    {
        if(!$this->hasTable('setting')) {
            $table = $this->table('setting', [
                'id' => false,
                'comment' => '设置记录表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => '设置ID', 'null' => false, 'signed' => false, 'identity' => true])
                ->addColumn('key', 'string', ['comment' => '设置项标示', 'null' => false, 'limit' => 30])
                ->addColumn('title', 'string', ['comment' => '设置标题', 'null' => false, 'limit' => 255, 'default' => ''])
                ->addColumn('describe', 'string', ['comment' => '设置项描述', 'null' => false, 'limit' => 255, 'default' => ''])
                ->addColumn('values', 'string', ['comment' => '设置值', 'null' => false, 'limit' => 500, 'default' => ''])
                ->addColumn('type', 'string', ['comment' => '设置类型', 'null' => false, 'limit' => 255, 'default' => ''])
                ->addColumn('options', 'string', ['comment' => 'options配置', 'null' => false, 'limit' => 500, 'default' => ''])
                ->addColumn('props', 'string', ['comment' => 'options配置', 'null' => false, 'limit' => 500, 'default' => ''])
                ->addColumn('group_id', 'integer', ['comment' => '分组ID', 'null' => false, 'default' => 0])
                ->addColumn('sort', 'integer', ['comment' => '排序', 'null' => false, 'default' => 0])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('key',  ['unique' => true, 'type' => 'btree'])
                ->addIndex(['group_id'], ['type' => 'btree'])
                ->create();
        }
    }

    public function settingGroup(): void
    {
        if(!$this->hasTable('setting_group')) {
            $table = $this->table('setting_group', [
                'id' => false,
                'comment' => '设置分组表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => '设置分组ID', 'null' => false, 'signed' => false, 'identity' => true])
                ->addColumn('pid', 'integer', ['comment' => '父ID', 'null' => false, 'signed' => false, 'default' => 0])
                ->addColumn('title', 'string', ['comment' => '分组标题', 'null' => false, 'limit' => 255])
                ->addColumn('key', 'string', ['comment' => '分组KEY', 'null' => false, 'limit' => 255])
                ->addColumn('type', 'string', ['comment' => '分组类型1：设置菜单 2：设置组 ', 'null' => false, 'limit' => 10, 'default' => '1'])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('key', ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function token(): void
    {
        if(!$this->hasTable('token')) {
            $table = $this->table('token', [
                'id' => false,
                'comment' => '用户Token表',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('token', 'string', ['comment' => 'Token', 'null' => false, 'limit' => 50])
                ->addColumn('type', 'string', ['comment' => '类型', 'null' => false, 'limit' => 15])
                ->addColumn('user_id', 'integer', ['comment' => '用户ID', 'null' => false, 'signed' => false])
                ->addColumn('expire_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '过期时间'])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('token', ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function user(): void
    {
        if(!$this->hasTable('user')) {
            $table = $this->table('user', [
                'id' => false,
                'comment' => '用户列表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => '用户ID', 'null' => false, 'signed' => false, 'identity' => true])
                ->addColumn('mobile', 'string', ['comment' => '手机号', 'null' => false, 'limit' => 11])
                ->addColumn('username', 'string', ['comment' => '用户名', 'null' => false, 'limit' => 50])
                ->addColumn('email', 'string', ['comment' => '用户邮箱', 'null' => false, 'limit' => 255, 'default' => ''])
                ->addColumn('password', 'string', ['comment' => '用户邮箱', 'null' => false, 'limit' => 60, 'default' => ''])
                ->addColumn('nickname', 'string', ['comment' => '昵称', 'null' => false, 'limit' => 50, 'default' => ''])
                ->addColumn('avatar_id', 'integer', ['comment' => '头像ID', 'null' => false, 'default' => 0, 'signed' => false])
                ->addColumn('gender', 'string', ['comment' => '性别', 'null' => false, 'default' => '0', 'limit' => 1])
                ->addColumn('birthday', 'date', ['comment' => '生日', 'null' => true])
                ->addColumn('group_id', 'integer', ['comment' => '分组ID', 'null' => false, 'default' => 1, 'signed' => false])
                ->addColumn('money', 'float', ['comment' => '用户余额', 'null' => false, 'default' => 0, 'signed' => true])
                ->addColumn('score', 'integer', ['comment' => '积分', 'null' => false, 'default' => 0, 'signed' => false])
                ->addColumn('motto', 'string', ['comment' => '签名', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('status', 'string', ['comment' => '状态', 'null' => false, 'default' => '1', 'limit' => 1])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('group_id', ['type'=>'btree'])
                ->addIndex('username', [ 'unique' => true, 'type'=>'btree' ])
                ->create();
        }
    }

    public function userMoneyLog(): void
    {
        if(!$this->hasTable('user_money_log')) {
            $table = $this->table('user_money_log', [
                'id' => false,
                'comment' => '用户余额变动明细表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => '记录ID', 'null' => false, 'signed' => false, 'identity' => true])
                ->addColumn('user_id', 'integer', ['comment' => '用户ID', 'null' => false, 'signed' => false ])
                ->addColumn('scene', 'string', ['comment' => '余额变动场景', 'null' => false, 'default' => '0', 'limit' => 1 ])
                ->addColumn('money', 'float', ['comment' => '余额变动场景', 'null' => false, 'default' => 0.00])
                ->addColumn('describe', 'string', ['comment' => '描述/说明', 'null' => false, 'default' => '', 'limit' => 500])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex('user_id', ['type'=>'btree'])
                ->create();
        }
    }

    public function userGroup(): void
    {
        if (!$this->hasTable('user_group')) {
            $table = $this->table('user_group', [
                'id' => false,
                'comment' => '会员分组表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('pid', 'integer', ['comment' => '上级分组ID', 'default' => 0, 'signed' => false, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '分组名称', 'null' => false])
                ->addColumn('rules', 'text', ['null' => true, 'default' => null, 'comment' => '权限ID'])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

    public function userRule(): void
    {
        if (!$this->hasTable('user_rule')) {
            $table = $this->table('user_rule', [
                'id' => false,
                'comment' => '会员权限规则表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('pid', 'integer', ['comment' => '父ID', 'null' => false, 'default' => 0, 'limit' => 10])
                ->addColumn('type', 'string', ['comment' => '类型 0：页面 1：数据 2：按钮', 'null' => false, 'default' => 0, 'limit' => 1])
                ->addColumn('sort', 'integer', ['comment' => '排序', 'null' => false, 'default' => 0])
                ->addColumn('name', 'string', ['comment' => '标题', 'null' => false, 'default' => '', 'limit' => 50])
                ->addColumn('path', 'string', ['comment' => '路由地址', 'null' => true, 'default' => '', 'limit' => 50])
                ->addColumn('icon', 'string', ['comment' => '图标', 'null' => true, 'default' => '', 'limit' => 255])
                ->addColumn('key', 'string', ['comment' => '权限标识', 'null' => false, 'limit' => 50])
                ->addColumn('locale', 'string', ['comment' => '国际化标识', 'null' => true, 'default' => '', 'limit' => 50])
                ->addColumn('status', 'integer', ['comment' => '启用状态', 'null' => false, 'default' => 1, 'limit' => 1])
                ->addColumn('show', 'integer', ['comment' => '显示状态', 'null' => false, 'default' => 1, 'limit' => 1])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->addIndex(['key'], ['unique' => true, 'type' => 'btree'])
                ->create();
        }
    }

    public function verificationCode(): void
    {
        if (!$this->hasTable('verification_code')) {
            $table = $this->table('verification_code', [
                'id' => false,
                'comment' => '验证码记录表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('type', 'string', ['comment' => '类型', 'null' => false, 'default' => '', 'limit' => 20])
                ->addColumn('code', 'integer', ['comment' => '验证码', 'null' => false, 'signed' => false])
                ->addColumn('status', 'integer', ['comment' => '状态0：未发送 1：已发送 2：已验证', 'null' => false, 'default' => 0,  'signed' => false])
                ->addColumn('interval', 'integer', ['comment' => '有效期', 'null' => false, 'default' => 0,  'signed' => false])
                ->addColumn('data', 'string', ['comment' => '接收方', 'null' => false, 'default' => '', 'limit' => 255])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '更新时间'])
                ->addColumn('update_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '创建时间'])
                ->create();
        }
    }

}
