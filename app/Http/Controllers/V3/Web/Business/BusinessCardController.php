<?php

namespace App\Http\Controllers\V3\Api\Web\Business;

use App\Logic\V3\Web\Business\BusinessCard;
use App\Http\Controllers\Controller;



class BusinessCardController extends Controller
{


    /**收款码
     * @return array
     */
    public function cardList(){
        $this->validate(null, [
            'businessCardId' =>'required|integer',
        ]);
        $businessCard = new BusinessCard();
        $businessCard->load($this->verifyData);
        $res = $businessCard->cardList();
        return [
            'res' => $res,
        ];
    }
}