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
namespace app\admin\controller\file;

use app\admin\controller\Controller;
use app\admin\model\file\FileModel as UploadFileModel;
use app\common\attribute\Auth;
use app\common\enum\FileType as FileTypeEnum;
use app\common\library\storage\Storage as StorageDriver;
use Exception;
use think\response\Json;

class UploadController extends Controller
{

    protected string $authName = 'file.upload';

    /**
     * @param int $groupId 分组ID
     * @return Json
     * @throws Exception
     */
    #[Auth('image')]
    public function image(int $groupId = 0): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件
        $storage->setUploadFile('file');
        // 设置上传验证规则
        $storage->setValidationScene(FileTypeEnum::image());
        // 执行文件上传
        if (!$storage->upload()) {
            return $this->error('图片上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel;
        $user_id = Auth::getAdminId();
        $model->add($fileInfo, FileTypeEnum::image(), $user_id, $groupId);
        // 图片上传成功
        return $this->success(['fileInfo' => $model->toArray()], '图片上传成功');
    }

    /**
     * @param int $groupId 分组ID
     * @return Json
     * @throws Exception
     */
    #[Auth('video')]
    public function video(int $groupId = 0): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件的信息
        $storage->setUploadFile('file');
        // 设置上传验证规则
        $storage->setValidationScene(FileTypeEnum::video());
        // 执行文件上传
        if (!$storage->upload()) {
            return $this->error('视频上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel;
        $user_id = Auth::getAdminId();
        $model->add($fileInfo, FileTypeEnum::video(), $user_id, $groupId);
        // 上传成功
        return $this->success(['fileInfo' => $model->toArray()], '视频上传成功');
    }

    /**
     * @param int $groupId 分组ID
     * @return Json
     * @throws Exception
     */
    #[Auth('zip')]
    public function zip(int $groupId = 0): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件的信息
        $storage->setUploadFile('file');
        // 设置上传验证规则
        $storage->setValidationScene(FileTypeEnum::zip());
        // 执行文件上传
        if (!$storage->upload()) {
            return $this->error('压缩包上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel;
        $user_id = Auth::getAdminId();
        $model->add($fileInfo, FileTypeEnum::zip(), $user_id, $groupId);
        // 上传成功
        return $this->success(['fileInfo' => $model->toArray()], '压缩包传成功');
    }

    /**
     * @param int $groupId 分组ID
     * @return Json
     * @throws Exception
     */
    #[Auth('mp3')]
    public function mp3(int $groupId = 0): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件的信息
        $storage->setUploadFile('file');
        // 设置上传验证规则
        $storage->setValidationScene(FileTypeEnum::mp3());
        // 执行文件上传
        if (!$storage->upload()) {
            return $this->error('音频文件上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel;
        $user_id = Auth::getAdminId();
        $model->add($fileInfo, FileTypeEnum::mp3(), $user_id, $groupId);
        // 上传成功
        return $this->success(['fileInfo' => $model->toArray()], '音频文件传成功');
    }

    /**
     * @param int $groupId 分组ID
     * @return Json
     * @throws Exception
     */
    #[Auth('annex')]
    public function annex(int $groupId = 0): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件的信息
        $storage->setUploadFile('file');
        // 设置上传验证规则
        $storage->setValidationScene(FileTypeEnum::annex());
        // 执行文件上传
        if (!$storage->upload()) {
            return $this->error('文件上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel;
        $user_id = Auth::getAdminId();
        $model->add($fileInfo, FileTypeEnum::annex(), $user_id, $groupId);
        // 上传成功
        return $this->success(['fileInfo' => $model->toArray()], '文件传成功');
    }

}