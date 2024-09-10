<?php

namespace app\api\validate;

use think\Validate;

class Install extends Validate
{

    protected $rule = [
        'mysql_hostname'    => 'require',
        'mysql_username'    => 'require',
        'mysql_password'    => 'require',
        'mysql_port'        => 'require',
        'mysql_name'        => 'require',
        'mysql_prefix'      => 'require',
        'web_title'         => 'require',
        'username'          => 'require',
        'password'          => 'require',
    ];

}