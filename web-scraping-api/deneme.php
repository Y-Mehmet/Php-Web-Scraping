<?php
require_once("getProductUrl.php");
//header("Content-Type: application/json");
$getProductUrlClass= new getProductUrl();

$urls= $getProductUrlClass->getProductsUrl();
$multiHandle = curl_multi_init();
$easyHandles = array();
// Create easy-handle objects for each request
if(isset($urls))
{
    foreach ($urls as $url) {
        echo "\n";
        echo json_encode(array(
            "productUrl"=> $url
        ));
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $easyHandles[] = $handle;
        }
}
else
{
    echo json_encode(array(
        "status"=>" 0",
        "mesage"=>" urls is null"
    ));
}

// Add each easy-handle object to the multi-handle object
foreach ($easyHandles as $handle) {
curl_multi_add_handle($multiHandle, $handle);
}

// Execute the multi-handle object
$running = null;
do {
curl_multi_exec($multiHandle, $running);
} while ($running);
// Retrieve response data for each request
$responses = [];
foreach ($easyHandles as $handle) {
// $info = curl_getinfo($handle);

$responses[] = curl_multi_getcontent($handle);
}
// Clean up
foreach ($easyHandles as $handle) {
curl_multi_remove_handle($multiHandle, $handle);
curl_close($handle);
}
curl_multi_close($multiHandle);

$productsIdDatas=[];
for($i=0; $i< count($responses);$i++)
{
    preg_match_all('/ItemActivityTicker.Start\(\s*(\d+)\s*\)/', $responses[$i],$amountProductId);
    if(isset($amountProductId) )
    $productsIdDatas=array_merge($productsIdDatas,$amountProductId[1]);
    else
    echo "eslesme sağlanammadı id: ".$id;
}

$productsUrl=[];

for ($i=0; $i <count($productsIdDatas) ; $i++) { 
    $url="https://steamcommunity.com/market/itemordershistogram?country=TR&language=turkish&currency=1&item_nameid=".$productsIdDatas[$i];
    $productsUrl[$i]= $url; 
}


$multiHandle2= curl_multi_init();
$easyHandles2= array();
if(isset($productsUrl))
{
    foreach ($productsUrl as $url) {
        echo "\n";
        echo json_encode(array(
            "productUrl"=> $url
        ));
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $easyHandles2[] = $handle;
        }
}
else
{
    echo json_encode(array(
        "status"=>" 0",
        "mesage"=>" urls is null"
    ));
}
foreach ($easyHandles2 as $handle) {
    curl_multi_add_handle($multiHandle2, $handle);
    }

// Execute the multi-handle object
$running = null;
do {
curl_multi_exec($multiHandle2, $running);
} while ($running);
// Retrieve response data for each request
$responses = [];
foreach ($easyHandles2 as $handle) {
// $info = curl_getinfo($handle);

$responses[] = curl_multi_getcontent($handle);
}
// Clean up
foreach ($easyHandles2 as $handle) {
curl_multi_remove_handle($multiHandle2, $handle);
curl_close($handle);
}
curl_multi_close($multiHandle2);
for ($i=0; $i < $responses; $i++) {

    $respons= $responses[$i];
    $data = json_decode($respons, true);

// DOMDocument ile tabloyu ayrıştıralım
$dom = new DOMDocument;
libxml_use_internal_errors(true); // HTML hatalarını yok saymak için

$dom->loadHTML($data['sell_order_table']);
libxml_clear_errors();

// Tablo hücrelerini al
$rows = $dom->getElementsByTagName('tr');

// Fiyatları alalım
$prices = [];
foreach ($rows as $row) {
    $cells = $row->getElementsByTagName('td');
    if ($cells->length > 0) {
        $price = trim($cells->item(0)->textContent); // İlk sütun: fiyat
        $quantity = trim($cells->item(1)->textContent); // İkinci sütun: miktar
        $prices[] = ['price' => $price, 'quantity' => $quantity];
    }
}

// Sonuçları yazdır
echo" ürün id: ".$productsIdDatas[$i]."\n";
for ($j=0; $j < 4 ; $j++) { 
    print_r($prices[$j]);
}
}
