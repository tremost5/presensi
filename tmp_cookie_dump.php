<?php
$base='http://127.0.0.1:8000';
$cookie=tempnam(sys_get_temp_dir(),'ck_');
$ch=curl_init($base.'/login');curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);$login=curl_exec($ch);curl_close($ch);
preg_match('/name="_token"\s+value="([^"]+)"/',$login,$tm);preg_match('/placeholder="\s*(\d+)\s*\+\s*(\d+)\s*=\s*\?\s*"/',$login,$cm);
$t=$tm[1]??'';$cap=((int)($cm[1]??0)+(int)($cm[2]??0));
$ch=curl_init($base.'/login');curl_setopt_array($ch,[CURLOPT_POST=>1,CURLOPT_POSTFIELDS=>http_build_query(['_token'=>$t,'username'=>'demo_superadmin','password'=>'Demo123','captcha'=>$cap]),CURLOPT_RETURNTRANSFER=>1,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);curl_exec($ch);curl_close($ch);
$ch=curl_init($base.'/superadmin/tingkat');curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);curl_exec($ch);curl_close($ch);
echo file_get_contents($cookie),"\n";
