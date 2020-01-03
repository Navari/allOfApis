<?php
require 'system/simple_html_dom.php';
require 'system/Larus.php';
ini_set('memory_limit','1024MB');
use Yilmaz\Larus;

$requestedURL = array_values(array_filter(explode('/',$_SERVER["REQUEST_URI"])));
array_shift($requestedURL);
$init = new Larus;
if(count($requestedURL) > 2){
    echo $init->json(303,'Hata !');
} else {
    $api = @$requestedURL[0];
    if(!isset($api)){
        echo $init->json(303,['message' => 'Erişmek istediğiniz URL Bulunamadı','availMethods' => 'weather - namaz - doviz - yakit']);
    }else {
        $queryString = @$requestedURL[1];
        echo $init->getApi($api, $queryString);
    }
}
