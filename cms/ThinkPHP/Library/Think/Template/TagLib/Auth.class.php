<?php
/**
* 用户权限解析库
* 杨俊编写2016-1-24 20:19:03
*/
namespace Think\Template\TagLib;
use Think\Template\TagLib;

class Auth extends TagLib {

    // 标签定义
    protected $tags   =  array(
    	// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'auth'      =>  array('attr'=>'rule,fullrule','close'=>1,'level'=>2),
    );

    /**
     * authIn标签解析
     * 格式：
     * <auth rule="Admin/User/add" fullrule="true" >显示按钮<else />不显示按钮</auth>
     * <auth rule="add">显示按钮<else />不显示按钮</auth>
     * @return string
     */
    public function _auth($tag,$content) {
        $rule  =   $this->parseCondition($tag['rule']);
        $fullrule = strtolower( $this->parseCondition($tag['fullrule']) );
        if($fullrule!='true'){
        	$rule = CONTROLLER_NAME.'/'.$rule;
        }
		$auth = new \Think\Auth();
		$rs = $auth->check($rule,1) ? 'true' : 'false';		//★★★我的系统里获取用户ID是getUid()方法，具体请根据自己的项目进行更改。★★★
        $parseStr   =   '<?php if( '.$rs.' ): ?>'.$content.'<?php endif; ?>';
        return $parseStr;
    }

}
