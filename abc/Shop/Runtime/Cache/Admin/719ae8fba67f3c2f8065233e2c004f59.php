<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body>
<form id="form1" name="form1" method="post" action="<?php echo U('Order/handle');?>" style="margin-left: 400px;">
  <!-- <input type="text" name="year" id="textfield" /> -->
  	<div style="margin-left:120px;"><h2>秒杀开始时间</h2></div>
  	<select name="year" id="select_k1">
  		<option value="">--年份--</option>
    	<option value="2017">2017</option>
		<option value="2018">2018</option>
   </select>

   <select name="month" id="select_k1">
  		<option value="">--月份--</option>
    	<option value="1">1月</option>
		<option value="2">2月</option>
		<option value="3">3月</option>
		<option value="4">4月</option>
		<option value="5">5月</option>
		<option value="6">6月</option>
		<option value="7">7月</option>
		<option value="8">8月</option>
		<option value="9">9月</option>
		<option value="10">10月</option>
		<option value="11">11月</option>
		<option value="12">12月</option>
   </select>
	

   <select name="day" id="select_k1">
  		<option value="">--日份--</option>
    	<option value="1">1日</option>
		<option value="2">2日</option>
		<option value="3">3日</option>
		<option value="4">4日</option>
		<option value="5">5日</option>
		<option value="6">6日</option>
		<option value="7">7日</option>
		<option value="8">8日</option>
		<option value="9">9日</option>
		<option value="10">10日</option>
		<option value="11">11日</option>
		<option value="12">12日</option>
		<option value="13">13日</option>
    	<option value="14">14日</option>
		<option value="15">15日</option>
		<option value="16">16日</option>
		<option value="17">17日</option>
		<option value="18">18日</option>
		<option value="19">19日</option>
		<option value="20">20日</option>
		<option value="21">21日</option>
		<option value="22">22日</option>
		<option value="23">23日</option>
		<option value="24">24日</option>
		<option value="25">25日</option>
		<option value="26">26日</option>
    	<option value="27">27日</option>
		<option value="28">28日</option>
		<option value="29">29日</option>
		<option value="30">30日</option>
   </select>

   	<select name="time" id="select_k1">
  		<option value="">--时间--</option>
    	<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
    	<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
		<option value="19">19</option>
		<option value="20">20</option>
		<option value="21">21</option>
		<option value="22">22</option>
		<option value="23">23</option>
		<option value="0">0</option>
	</select>

		<select name="branch" id="select_k1">
  		<option value="">--分钟--</option>
    	<option value="1">1分</option>
		<option value="2">2分</option>
		<option value="3">3分</option>
		<option value="4">4分</option>
		<option value="5">5分</option>
		<option value="6">6分</option>
		<option value="7">7分</option>
		<option value="8">8分</option>
		<option value="9">9分</option>
		<option value="10">10分</option>
		<option value="11">11分</option>
		<option value="12">12分</option>
		<option value="13">13分</option>
    	<option value="14">14分</option>
		<option value="15">15分</option>
		<option value="16">16分</option>
		<option value="17">17分</option>
		<option value="18">18分</option>
		<option value="19">19分</option>
		<option value="20">20分</option>
		<option value="21">21分</option>
		<option value="22">22分</option>
		<option value="23">23分</option>
		<option value="24">24分</option>
    	<option value="25">25分</option>
		<option value="26">26分</option>
		<option value="27">27分</option>
		<option value="28">28分</option>
		<option value="29">29分</option>
		<option value="30">30分</option>
		<option value="31">31分</option>
		<option value="32">32分</option>
		<option value="33">33分</option>
		<option value="34">34分</option>
		<option value="35">35分</option>
		<option value="36">36分</option>
		<option value="37">37分</option>
    	<option value="38">38分</option>
		<option value="39">39分</option>
		<option value="40">40分</option>
		<option value="41">41分</option>
		<option value="42">42分</option>
		<option value="43">43分</option>
		<option value="44">44分</option>
		<option value="45">45分</option>
		<option value="46">46分</option>
		<option value="47">47分</option>
		<option value="48">48分</option>
		<option value="49">49分</option>
    	<option value="50">50分</option>
		<option value="51">51分</option>
		<option value="52">52分</option>
		<option value="53">53分</option>
		<option value="54">54分</option>
		<option value="55">55分</option>
		<option value="56">56分</option>
		<option value="57">57分</option>
		<option value="58">58分</option>
		<option value="59">59分</option>
		<option value="0">0分</option>
	</select>
	<br>
   <input type="submit" value="提交" style="width:60px;height:40px;font-size: 20px;background-color: #F8F8F8;margin-top: 30px;border:none;color:#000;border:1px solid #000000;margin-left:160px;"/>
</form>
</body>
</html>