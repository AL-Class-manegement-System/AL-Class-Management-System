<?php

$merchant_id = "1231876";
$order_id = uniqid();
$amount = 4000;
$currency = "LKR";
$merchant_secret = "MTUyNTg1MzQyNDIyMTc3MTE0ODIzMTIyNzU0NTg4Njk2NDEwMTYw";
$item=array('jjjjj','kkjkkk');
$hash = strtoupper(
    md5(
        $merchant_id .
        $order_id .
        number_format($amount, 2, '.', '') .
        $currency .
        strtoupper(md5($merchant_secret))
    )
);

$valueArray = [];
$valueArray["merchant_id"] = $merchant_id;
$valueArray["order_id"] = $order_id;
$valueArray["amount"] = $amount;
$valueArray["currency"] = $currency;
$valueArray["item"] = $item;
$valueArray["hash"] = $hash;

$jsonObj = json_encode($valueArray);

echo $jsonObj;
