<?php

namespace App\Http\Controllers\V10\Api;

use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    protected $upload;

    public function __construct()
    {
        method_exists(parent::class, '__construct') && parent::__construct();
        $this->fileConfigInit();
    }

    private function fileConfigInit()
    {
        $config = [
            'uid' => '0'
        ];
        // 使用存储驱动
        $drivers = new \DdvPhp\DdvFile\Drivers\AliyunOssDrivers(config('aliyun.star'));
        // 数据库模型
        $database = new \DdvPhp\DdvFile\Database\LaravelMysqlDatabase();
        $this->upload = new \DdvPhp\DdvFile($config, $drivers, $database);
    }

    ## 获取分块大小
    public function filePartSize(Request $request)
    {
        return [
            'data' =>
                $this->upload->getPartSize($request->only(['fileSize', 'fileType', 'deviceType']))
        ];
    }

    public function fileId(Request $request)
    {
        $input = $request->only(
            $this->upload->getFileIdInputKeys([
                'authType',
                'manageType',
                // 上传目录
                'directory'
            ])
        );
        $directorys = config('upload.directory');
        $input['directory'] = empty($directorys[$input['authType']]) ? '/upload/other/' : $directorys[$input['authType']];
//    switch($input['authType']){
//    }
        return [
            'data' =>
                $this->upload->getFileId($input)
        ];
    }

    public function filePartInfo(Request $request)
    {
        $input = $request->only(
            [
                'fileId',
                'fileMd5',
                'fileSha1',
                'fileCrc32'
            ]
        );
        return [
            'data' =>
                $this->upload->getFilePartInfo($input)
        ];
    }

    public function filePartMd5(Request $request)
    {
        $input = $request->only(
            [
                'fileId',
                'fileMd5',
                'fileSha1',
                'fileCrc32',


                'md5Base64',
                'partLength',
                'partNumber'
            ]
        );
        return [
            'data' =>
                $this->upload->getFilePartMd5($input)
        ];

    }

    public function complete(Request $request)
    {
        $input = $request->only(
            [
                'fileId',
                'fileMd5',
                'fileSha1',
                'fileCrc32'
            ]
        );
        $data = $this->upload->complete($input);
        return [
            'data' => $data

        ];
    }
}