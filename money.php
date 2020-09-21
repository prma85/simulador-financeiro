<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//1/0.62287
setlocale(LC_ALL, "en_US.utf8");
date_default_timezone_set('America/Regina');

$response = file_get_contents("http://api.fixer.io/latest?base=CAD&symbols=USD,BRL,EUR,GBP");
$money = json_decode($response);
$money->rates->GBP2 = 1/$money->rates->GBP;
$money->rates->EUR2 = 1/$money->rates->EUR;
$money->rates->USD2 = 1/$money->rates->USD;

$buy = array();
if ($money->rates->EUR2 <= 1.45) {
    $buy[] = 'Comprar euro, preço atual de CAD$'.$money->rates->EUR2;
}
if ($money->rates->GBP2 <= 1.6) {
    $buy[] = 'Comprar libra, preço atual de CAD$'.$money->rates->EUR2;
}
if ($money->rates->USD2 <= 1.22) {
    $buy[] = 'Comprar dolar, preço atual de CAD$'.$money->rates->USD2;
}
if ($money->rates->USD2 >= 1.32) {
    $buy[] = 'Vender dolar, preço atual de CAD$'.$money->rates->USD2;
}
if ($money->rates->BRL >= 2.62) {
    $buy[] = 'Comprar real, preço atual de CAD$'.$money->rates->BRL;
}
if ($money->rates->BRL <= 2.5) {
    $buy[] = 'Vender real, preço atual de CAD$'.$money->rates->BRL;
}
var_dump($money);

echo implode(', ', $buy);
