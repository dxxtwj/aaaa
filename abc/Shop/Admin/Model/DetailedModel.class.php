<?php
namespace Admin\Model;
use Think\Model;

class DetailedModel extends Model{ 

	protected $_validate = [

		// 口味
		['kouwei', 'require', '口味不能为空'],

		['kouwei', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的口味'],
		['kouwei', '', '改口味已存在', 0, 'unique', 3],




		// 包装
		// ['baozhuang', 'require', '包装不能为空'],
		// ['baozhuang', '', '该包装已存在', 0, 'unique', 3],
		// ['baozhuang', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的包装'],


	];

}