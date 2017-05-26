<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);


function getAllHeaders() {
    $headers = array();
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

$content = file_get_contents('php://input');

$headers = getAllHeaders();
$header_joins = array();
foreach ($headers as $k => $v) {
    if ($k == 'X-Pingplusplus-Signature' || $k == 'Content-Type')
    array_push($header_joins, $k . ': ' . $v);
}

print_r($header_joins);die();

function post($url, $headers, $raw_data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  // POST 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_data);  // Post Data
    curl_setopt($ch, CURLOPT_URL, $url);//����Ҫ���ʵ� URL
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); //ģ���û�ʹ�õ������
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );  // ʹ���Զ���ת
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);  //���ó�ʱʱ��
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1 ); // �Զ�����Referer
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // �ռ��������ֱ��չʾ
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // �Զ��� Headers
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
