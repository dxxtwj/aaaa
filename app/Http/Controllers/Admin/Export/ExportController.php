<?php

namespace App\Http\Controllers\Admin\Export;

use App\Logic\V2\User\WechatLogic;
use App\Logic\Works\WorksLogic;
use DdvPhp\DdvRestfulApi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    public function export(){
        $this->verify(
            [
                'worksCateId' => '',//类型
            ]
            , 'POST');
         /*$query = [
         	'user_id'=>'dsf111',
         	'dddd2'=>'dsfsdf',
         	'aaaa'=>'aaaaa',
         	'bbb'=>'bbb',
         	'cc'=>'cc'
         ];*/
        // $query=array(1,2,3);
        $res = WorksLogic::checkWorksByCateId($this->verifyData['worksCateId']);
        if(empty($res)){
            throw new DdvRestfulApi\Exception\RJsonError("该类下没有作品", 'NO_WORKS');
        }
        $query['worksCateId']=$this->verifyData['worksCateId'];
        $url = 'http://api.shangrui.cc/v1.0/export?'.\DdvPhp\DdvUrl::buildQuery($query);
        // 签名url是的地址可以直接在浏览器打开
        $restfulApi = DdvRestfulApi::getInstance();
        $url = $restfulApi->getSignUrlByUrl($url, [], 'GET', $query);
        return ['data'=>['UserUrl'=>$url]];
    }
    public function getExport(){
        $res=$_GET;
        //var_dump($res['worksCateId']);
        //$res['worksCateId']=14;
        $arr = WorksLogic::getWorksByCateId($res['worksCateId']);
        //return ['lists'=>$arr];
        //$file = __ROOT_PATH__.'/exex/user' . date('Y-m-d');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '赛区');
        $sheet->setCellValue('B1', '作品名称');
        $sheet->setCellValue('C1', '作品编号');
        $sheet->setCellValue('D1', '作者');
        $sheet->setCellValue('E1', '投票数');
        $sheet->setCellValue('F1', '赛区排名');
        $num=2;
        if(!empty($arr)){
            foreach ($arr as $key => $value) {
                $sheet->setCellValue('A'.$num, $value['worksCateTitle']);
                $sheet->setCellValue('B'.$num, $value['worksTitle']);
                $sheet->setCellValue('C'.$num, $value['worksNumber']);
                $sheet->setCellValue('D'.$num, $value['name']);
                $sheet->setCellValue('E'.$num, $value['worksVote']);
                $sheet->setCellValue('F'.$num, $value['ranking']);
                $num =$num+1;
                $worksCateTitle=$value['worksCateTitle'].'排名';
            }
        }

        $writer = new Xlsx($spreadsheet);

        // 输出的名字
        $filename="$worksCateTitle.xlsx";

        // 生成临时存储位置
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        // 写入到临时文件
        $writer->save($tempFile);

        //弹出下载对话框
        header('Content-Type: application/vnd.ms-excel');
        // 指定保存的名字
        header('Content-Disposition: attachment; filename=' . $filename);

        // 读取那个临时文件，并且输出浏览器
        readfile($tempFile);
        // 删除那个临时文件
        unlink($tempFile);
    }

    public function exportUser(){
        $this->verify(
            [
                'siteId' => '',//类型
                'numberId' => '',//类型
                'number' => '',//类型
            ]
            , 'POST');
        $query['siteId']=$this->verifyData['siteId'];
        $query['numberId']=$this->verifyData['numberId'];
        $query['number']=$this->verifyData['number'];
        $url = 'http://api.shangrui.cc/v1.0/export/user?'.\DdvPhp\DdvUrl::buildQuery($query);
        // 签名url是的地址可以直接在浏览器打开
        $restfulApi = DdvRestfulApi::getInstance();
        $url = $restfulApi->getSignUrlByUrl($url, [], 'GET', $query);
        return ['data'=>['UserUrl'=>$url]];
    }
    public function getExportUser(){
        $res=$_GET;
        $arr = WechatLogic::getWechatUser($res['siteId'],$res['numberId'],$res['number']);
        //$file = __ROOT_PATH__.'/exex/user' . date('Y-m-d');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '微信名称');
        $sheet->setCellValue('B1', '性别');
        $sheet->setCellValue('C1', '城市');
        $sheet->setCellValue('D1', '省');
        $sheet->setCellValue('E1', '国家');
        $sheet->setCellValue('F1', 'id');
        $num=2;
        if(!empty($arr)){
            foreach ($arr as $key => $value) {
                if($value['sex']==1){
                    $sex = '男';
                }else{
                    $sex = '女';
                }
                $sheet->setCellValue('A'.$num, $value['nickname']);
                $sheet->setCellValue('B'.$num, $sex);
                $sheet->setCellValue('C'.$num, $value['city']);
                $sheet->setCellValue('D'.$num, $value['province']);
                $sheet->setCellValue('E'.$num, $value['country']);
                $sheet->setCellValue('F'.$num, $value['openid']);
                $num =$num+1;
            }
        }

        $writer = new Xlsx($spreadsheet);

        // 输出的名字
        $filename="微信用户.xlsx";

        // 生成临时存储位置
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        // 写入到临时文件
        $writer->save($tempFile);

        //弹出下载对话框
        header('Content-Type: application/vnd.ms-excel');
        // 指定保存的名字
        header('Content-Disposition: attachment; filename=' . $filename);

        // 读取那个临时文件，并且输出浏览器
        readfile($tempFile);
        // 删除那个临时文件
        unlink($tempFile);
    }
    public function getWechatUser()
    {
        $this->verify(
            [
                'openid' => '',//类型
            ]
            , 'POST');
        $res = WechatLogic::getWechatUserByOpenid($this->verifyData['openid']);
        return ['data'=>$res];
    }

}