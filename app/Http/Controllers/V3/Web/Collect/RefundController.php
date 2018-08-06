<?php

namespace App\Http\Controllers\V3\Api\Web\Collect;

use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SiteSettings;
use \App\Logic\V3\Admin\Order\CollectLogic;

class RefundController extends Controller
{

    /**客户申请退款
     * @return array
     * @throws RJsonError
     */
    public function refund(){
        $this->validate(null, [
            'orderNumber' => 'required|string',
            'siteId' => 'required|integer'
        ]);
        $collectLogic = new CollectLogic();
        $collectLogic->load($this->verifyData);
        if($collectLogic->refund()){
            return [];
        }
        throw new RJsonError('申请退款失败', 'REFUND_COLLECT_ERROR');
    }
}