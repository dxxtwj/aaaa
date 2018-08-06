<?php

namespace App\Http\Controllers\Admin\Gallery;

use App\Logic\AboutLogic;
use App\Logic\Gallery\GalleryLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class GalleryController extends Controller
{
    //获取图库
    public function getImage()
    {
        //获取关于我们的图片
        $res = GalleryLogic::getGalleryLists();
        return $res;
    }

    //删除
    public function deleteImage()
    {
        $this->verify(
            [
                'gallery' => '',
            ]
            , 'POST');
        $res = GalleryLogic::deleteArray($this->verifyData['gallery']);
        return $res;
    }


}
