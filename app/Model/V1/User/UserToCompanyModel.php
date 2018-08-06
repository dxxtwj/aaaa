<?php

namespace App\Model\V1\User;
use App\Model\V1\Company\CompanyModel;
class UserToCompanyModel extends \App\Model\Model
{
    public $timestamps = false;

    protected $table = 'user_to_company';
    protected $primaryKey = 'uid';
    protected $timestamp = false;
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'update_at';

    public function company(){
        return $this->hasOne(CompanyModel::class, 'company_id', 'companyId');
    }

    public function userInfo(){
        return $this->hasOne(InfoModel::class, 'uid', 'uid');
    }
}
