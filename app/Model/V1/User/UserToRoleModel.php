<?php

namespace App\Model\V1\User;
use App\Model\V1\Role\RoleToAuthModel;

class UserToRoleModel extends \App\Model\Model
{
    public $timestamps = false;

    protected $table = 'user_to_role';
    protected $primaryKey = 'role_id';

    public function role(){
        return $this->hasOne(RoleToAuthModel::class, 'role_id', 'roleId');
    }
}
