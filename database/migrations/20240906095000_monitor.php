<?php

use think\migration\Migrator;

class Monitor extends Migrator
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
    public function change()
    {
        if (!$this->hasTable('monitor')) {
            $table = $this->table('monitor', [
                'id' => false,
                'comment' => '数据监控表',
                'primary_key' => 'id',
                'row_format' => 'DYNAMIC',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'null' => false,  'signed' => false, 'identity' => true])
                ->addColumn('name', 'string', ['limit' => 30, 'default' => '', 'comment' => '操作名称', 'null' => false])
                ->addColumn('controller', 'string', ['limit' => 30, 'default' => '', 'comment' => '控制器', 'null' => false])
                ->addColumn('action', 'string', ['limit' => 50, 'default' => '', 'comment' => '方法', 'null' => false])
                ->addColumn('data', 'text', ['null' => true, 'default' => null, 'comment' => 'POST参数'])
                ->addColumn('params', 'text', ['null' => true, 'default' => null, 'comment' => 'Params参数'])
                ->addColumn('user_id', 'integer', ['limit' => 10, 'comment' => '管理员ID', 'null' => false])
                ->addColumn('create_time', 'integer', ['limit' => 10, 'signed' => false, 'null' => true, 'default' => null, 'comment' => '访问时间'])
                ->create();
        }
        
    }
}
