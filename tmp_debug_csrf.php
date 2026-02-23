<?php
$base='http://127.0.0.1:8000';
$cookie=tempnam(sys_get_temp_dir(),'ck_');
function req($m,$u,$c,$d=null){$ch=curl_init($u);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_HEADER=>0,CURLOPT_COOKIEJAR=>$c,CURLOPT_COOKIEFILE=>$c]);if($m==='POST'){curl_setopt($ch,CURLOPT_POST,1);curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($d));} $b=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);return [$code,$b];}
function tok($h){if(preg_match('/name=["\']_token["\'][^>]*value=["\']([^"\']+)["\']/i',$h,$m)) return $m[1];if(preg_match('/value=["\']([^"\']+)["\'][^>]*name=["\']_token["\']/i',$h,$m)) return $m[1];if(preg_match('/meta\s+name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']/i',$h,$m)) return $m[1];return '';}
[$_, $login]=req('GET',$base.'/login',$cookie);preg_match('/placeholder="\s*(\d+)\s*\+\s*(\d+)\s*=\s*\?\s*"/',$login,$cm);$t=tok($login);$cap=((int)($cm[1]??0)+(int)($cm[2]??0));req('POST',$base.'/login',$cookie,['_token'=>$t,'username'=>'demo_superadmin','password'=>'Demo123','captcha'=>$cap]);
[$c,$body]=req('GET',$base.'/superadmin/tingkat',$cookie);
file_put_contents('tmp_last_tingkat.html',$body);
echo "code=$c tokenLen=".strlen(tok($body))."\n";
