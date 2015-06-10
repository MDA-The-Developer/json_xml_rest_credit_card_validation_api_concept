<?php

//  Imitation of getting data from somewhere
// POST JSON XML

//in different cases we can use database with REST... so i decided to add possibility of connection by pdo method in plugins
// also here i hashed word for phone validation


// for now here is 4 cases
// 1 json object with card data
// 2 json object with phone number
// 3 xml with card data
// 4 xml with phone number




//                  change this number to chose test (1,2,3,4)
define('TEST_NUMBER',1);

if(TEST_NUMBER==1){
    $json = '{"card":"5168755352683177","exp_date":"12/12","CVV2":277,"email":"example@mail.com"}';
    $_POST['json']=$json;
}

if(TEST_NUMBER==2){
    $json='{"phone":"+380687849001","passphrase":"acupoftea"}';
    $_POST['json_p']=$json;
}

if(TEST_NUMBER==3){
    // xml object
    $xml =
        "<?xml version='1.0' encoding='UTF-8'?>
<note>
<card>5168755352683177</card>
<exp_date>12/12</exp_date>
<cvv2>277</cvv2>
<email>example@mail.com</email>
</note>";
    $_POST['xml']=$xml;
}

if(TEST_NUMBER==4){
      $xml =
        "<?xml version='1.0' encoding='UTF-8'?>
<note>
<phone>+380687849001</phone>
<passphrase>acupoftea</passphrase>
</note>";
    $_POST['xml_p']=$xml;
}

///////       API    s t a r t
require_once $_SERVER['DOCUMENT_ROOT']. '/api/validation.php';

