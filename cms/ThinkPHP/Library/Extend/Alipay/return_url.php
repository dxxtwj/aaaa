<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号

	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号

	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];


    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	// echo "验证成功<br />";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$mysql_server_name="localhost"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名
    $mysql_password="d4d7cc5dae"; // 连接数据库密码
    $mysql_database="youka"; // 数据库的名字
    
    // 连接到数据库
    $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password) or die('连接失败');
    
      //3 选择数据库
 	 mysql_select_db($mysql_database);

 	$sql_uid= sprintf("SELECT `OR_UID` FROM `tb_order_rec` WHERE `OR_Key` = '%s'",$out_trade_no);
 	$res_uid=mysql_query($sql_uid);
    $row_uid=mysql_fetch_array($res_uid);
    $uid = $row_uid['OR_UID'];//查出上级

	
	//小计
	$sql_og= sprintf("SELECT `OG_Price`,`OG_Number`,`OG_GID`,`OG_GuigeID` FROM `tb_order_goods` WHERE `OG_OID`= (SELECT `OR_ID` FROM `tb_order_rec` WHERE `OR_Key` = '%s')",$out_trade_no);
    $res_small=mysql_query($sql_og);
    // $small_total = 0.00;
	$yongjin = 0.00;
    while($row=mysql_fetch_array($res_small)){
    	//减库存方式
    	if($row['OG_GID']!=null){
    		$less_sql = sprintf("SELECT `SG_Less` FROM `tb_shop_goodslist` WHERE `SG_ID`='%s'",$row['OG_GID']);
			$res_less=mysql_query($less_sql);
		    $row_less=mysql_fetch_array($res_less);
		    //减库存
		    if($row_less['SG_Less']==2){
		    	if($row['OG_GuigeID']!=0&&$row['OG_GuigeID']!=null){
					
					$stock_sql = sprintf("UPDATE `tb_format_option` SET `FO_Stock` = `FO_Stock`-'%s' WHERE `GF_ID`='%s'",1,$row['OG_GuigeID']);
					$stock_query=mysql_query($stock_sql);
				}else{
					$stock_sql = sprintf("UPDATE `tb_shop_goodslist` SET `SG_Stock` = `SG_Stock`-'%s' WHERE `SG_ID`='%s'",1,$row['OG_GID']);
					$stock_query=mysql_query($stock_sql);
					
				}
			}
    	}
    	

		// //加销量
		$addsale_sql = sprintf("UPDATE `tb_shop_goodslist` SET `SG_Rsale` = `SG_Rsale`+'%s' , `SG_Sale` = `SG_Sale`+'%s' WHERE `SG_ID`='%s'",1,1,$row['OG_GID']);
		$addsale_query=mysql_query($addsale_sql);
		echo $addsale_query;

    	
    }
    // echo " 小计:".$sql_og.",".$small_total."<br/>";


    $now = time();
    $sql = sprintf("UPDATE `tb_order_rec` SET `OR_Type` = '%s',`OR_State` = '%s',`OR_PayTime` = '%s' WHERE `OR_Key`='%s'","1","1",$now,"$out_trade_no");
	$query=mysql_query($sql);



    
	// $log_->log_result($log_name,"【支付成功】:\n".$out_trade_no." ".$trade_no.' '."\n"."$sql"."$query"."\n");
	// echo "<br/>".$yongjin.",".$query;
	echo "<script>window.location.href='/Application/Home/Mobile/Altogether/pay_success.html'</script>";

}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}
?>
        <title>支付宝手机网站支付接口</title>
	</head>
    <body>
    </body>
</html>