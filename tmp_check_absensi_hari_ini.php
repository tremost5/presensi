<?php
$base='http://127.0.0.1:8000';
$cookie=tempnam(sys_get_temp_dir(),'ck_');
function req($m,$u,$c,$d=null){$ch=curl_init($u);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_HEADER=>1,CURLOPT_FOLLOWLOCATION=>0,CURLOPT_COOKIEJAR=>$c,CURLOPT_COOKIEFILE=>$c]);if($m==='POST'){curl_setopt($ch,CURLOPT_POST,1);curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($d));} $raw=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);$hs=curl_getinfo($ch,CURLINFO_HEADER_SIZE);curl_close($ch);return [$code,substr($raw,$hs)];}
[$c1,$b1]=req('GET',$base.'/login',$cookie);preg_match('/name="_token"\s+value="([^"]+)"/',$b1,$tm);preg_match('/placeholder="\s*(\d+)\s*\+\s*(\d+)\s*=\s*\?\s*"/',$b1,$cm);$t=$tm[1]??'';$cap=((int)($cm[1]??0)+(int)($cm[2]??0));req('POST',$base.'/login',$cookie,['_token'=>$t,'username'=>'demo_guru','password'=>'Demo123','captcha'=>$cap]);
[$c2,$b2]=req('GET',$base.'/guru/absensi-hari-ini',$cookie);
file_put_contents('tmp_absensi_hari_ini.html',$b2);
echo "code=$c2 len=".strlen($b2)."\n";
if (preg_match('/Absensi Hari Ini/i',$b2)) echo "HAS_TITLE\n";
if (preg_match('/Belum ada absensi hari ini/i',$b2)) echo "HAS_EMPTY\n";
if (preg_match('/<table/i',$b2)) echo "HAS_TABLE\n";
