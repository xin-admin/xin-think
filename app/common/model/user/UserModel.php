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
namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\file\FileModel;
use app\common\model\user\UserMoneyLogModel as MoneyLogModel;
use think\model\relation\HasOne;

/**
 * 用户模型
 */
class UserModel extends BaseModel
{
    protected $name = 'user';

    protected $hidden = [
        'password', 'create_time', 'update_time'
    ];

    /**
     * 关联用户头像表
     * @return HasOne
     */
    public function avatar(): HasOne
    {
        return $this->hasOne(FileModel::class, 'file_id', 'avatar_id')
            ->bind(['avatar_url' => 'preview_url']);
    }


    /**
     * 累积用户的余额
     * @param int $userId 用户ID
     * @param float $money 累计的积分
     * @param string $describe 积分描述
     * @param int $scene 积分变动类型
     * @param array $data
     * @return mixed
     */
    public static function setIncMoney(int $userId, float $money, string $describe, int $scene, array $data = []): mixed
    {
        // 新增余额变动记录
        MoneyLogModel::add($userId, $scene, $money, $describe, $data);
        // 更新用户可用积分
        return (new static)->setInc($userId, 'money', $money);
    }

    /**
     * 累减用户的余额
     * @param int $userId 用户ID
     * @param float $money 累计的积分
     * @param string $describe 积分描述
     * @param int $scene 积分变动类型
     * @param array $data
     * @return mixed
     */
    public static function setDecMoney(int $userId, float $money, string $describe, int $scene, array $data = []): mixed
    {
        // 新增余额变动记录
        MoneyLogModel::add($userId, $scene, -$money, $describe, $data);
        // 更新用户可用积分
        return (new static)->setDec($userId, 'money', $money);
    }
}
