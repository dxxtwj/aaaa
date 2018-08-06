<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use App\Http\Middleware\ClientIp;

use JiaLeo\Laravel\Core\Controller as CoreController;
use \DdvPhp\DdvUtil\Laravel\Controller as DdvController;

class Controller extends BaseController
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //use DispatchesJobs,DdvController;
    // use AuthorizesRequests, DispatchesJobs, ;
    use DispatchesJobs,DdvController, ValidatesRequests;

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return void
     */
    public function validate($data = null, array $rules, array $messages = [], array $customAttributes = [])
    {
        if (empty($data)){
            $request = ClientIp::getRequest();
            $data = $request->all();
        }elseif (is_array($data)){
            $request = ClientIp::getRequest();
            $data = array_merge($data, $request->input());
        }else{
            $data = [];
        }
        $validator = $this->getValidationFactory()->make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            foreach ($validator->failed() as $key => $item){
                throw new RJsonError($key.'验证错误['.json_encode($item).']', strtoupper(\DdvPhp\DdvUtil\String\Conversion::humpToUnderline($key)) .'_ERROR');
            }
        }
        $this->verifyData = [];
        foreach ($rules as $key => $value){
            $this->verifyData[$key] = !isset($data[$key])?'':$data[$key];
        }
    }
}
