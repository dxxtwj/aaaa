<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2017121900980141",

		//商户私钥
		'merchant_private_key' => "MIIEpQIBAAKCAQEA8lnSexj0OPrIhzJz3IpJ4f7Hq6EpcDlN5oYlfcBN0Z85m70a8rVWjGjXcyjPIjhLVROp3fLqPngzwHcKRI2xh8AK7svs4IzhNE9JECXvQXFIe6JsCRWXE20+DkS7XrS2Vu7rjr/vOiTkhZFQmu/O5OTM60tZK5+fQ32yuYEZn2w+BZMpWq5B6PjncMrxXZ2Av2uR/8vrEISjKXHUcQ/0J8dNk3aSoDIcqqFb0v8jVYeNSSscqU0brmJU/aXJJNdBYdAu8MJ6vmVU+NTrt7cOWA1JZBJL2vqO/poNWpzoO78LU+VaoAnVKzenf2xlLn0Pv0OP9qtYOTvcl543IzDF4QIDAQABAoIBAENSGjrhoq3VVDDiHfcsXvutR4/wk889y9r2dqGo8rUBMwKFFGBJaUJVuUufEXLjCmIWtUAXQ2SZBV84chy6847PFPpioWl7GO/r+lLHBuA59LWLV9FqMu5NkUhrZVUYg/JAKiUcQfr31pcX17lCVkmVGStfYeTRUW9xEl2fdBupbmhFepLU42sxAfr8qjZzPVsAyIjNcAOQuSmg/epA6KGWj1tJScUeBsnW0pWbXE5H9UUg2Za5BHBgT3ukyQGYK13zz5+XjDvucIoYmDbYkUnv5Clu4PbJ4aMCfN7d/7m2nsa9naFp8EFLoPaBdVqOsQ+Ksp58pKjtCx5sZ07L6XUCgYEA/DuJ8IThnliio1u2dKjNRHaeRN7cBeRtoi67g0FyogHTA+SrNgWi0z0Rtes7nXofECGAvbJdZCFJPQF8+dksjN5xjXBkbipc8kekOTVLfyZMM+qsXdF35tbD1lW/b5gAtanvkZ9D6cBZTMnb1CJMBO0HzyoJGjF5oreLQDnkM5cCgYEA9fh/q0SWgq+Br+yoFG82wC7lzc1sP6lfXv5PVTt/3MsVHAaBJ8fpqkLjYbnzR2X8dNCennmUHUji7bcN+/cmu67gDNFHM2IBJqdUsRxx8Q8sTkfJXwfTOf6wfPHXSieK3E2b5Ss/I6mWSFefyKKAGsdEtVmomKcAczpYYO/AIUcCgYEA19648oE3e0bOgtLmN7eUXgjK8ZRuaBiU/93RG8eyrRa0mztkkIqITROSCcj2L5Op7CtQPInfxed6/9w7MrX8m99w/aT4PxTAkZZg5ZlIMNz9EN3CgVTZslWSK+kdOwuzZ8a1w0K0Y+T8SGzmWAq2Vuzp5xJJ0gIP4QQHCIPDW6cCgYEAwwdyU15i6OQ4NEG1U96Kzv9gfzouZpvpXuzOAorW6z6rUjlmFyDQCYxXtpk2aJpY/lacLpO/+ShdppbKo8poD9CtHfyiM/+1YeyznmsrfTPGsZsB9DPEK+viDG/FgKqx09RFlAQiYCiLXHj93Jvcb18o/5JDuCZSmQreIJ54/OkCgYEArCwC4uo/uBiT/sTSHLP3046eSZqAFFpMxoAsw+2avC2wQshiNUe+S4PiloFmTGlj1P/5CEl3E8PCHY9TNUDMI9mc9/qcHw+hApAminrX5jBVxvmIOUkIS29LF9M4DgDcmp9OR5/U+oBCx1ogD1sBogAQC59wuG3+iJl9vM6hXd8=",
		
		//异步通知地址
		'notify_url' => "http://yukitest.forhuman.cn/index.php/Home/Pay/ali_codepay_notify",
		
		//同步跳转
		'return_url' => "http://yukitest.forhuman.cn/index.php/Home/Pay/ali_codepay_return",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0+WBqOGyuX+By/sDU+KvyHwWKvmS/SosAbvo6CGn4KInmQNY/Sd0/u8C8cN1ds9Y3EFpIhpmDPjT5SA/7p8BC0ihUvilLIrEM7Kye0N06Xgrp2pZ1u9Vf4eDYg68CJ5TnD6OP/CFI4E7cISOhTOi2+awp1ol4TuyJgyUEWfw9m3XKhZkK+8tJYINaNOsEhW+4HyzbTLeuih/jHYY2ZCPizwuIPfw9y2KOC37LsqP7uoYxiahslOIBZ1RmrtUio5NVObSWV1Gblbkv0hqHYDcJRdp/ILJkF/haIFqdznzvnsjPCDD/vitD0kQ9RSua9Sqg5YnlDvNXB/+4VFt5xIX9wIDAQAB",
);