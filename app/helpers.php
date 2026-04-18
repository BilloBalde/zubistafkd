<?php

use Carbon\Carbon;
use GuzzleHttp\Promise\Is;


if (!function_exists('getNow')) {
    function getNow() {
        return Carbon::now();
    }
}

if(!function_exists('getImage')){
    function getImage($folder, $value){
        return asset($folder.''.$value);
    }
}
if(!function_exists('containsAll')){
    function containsAll($string, $substrings) {
        foreach ($substrings as $substring) {
            if (strpos($string, $substring) === false) {
                return false;
            }
        }
        return true;
    }
}
if(!function_exists('numberDelimiter')) {
    function numberDelimiter($number){
        return number_format($number, 2, '.', ',');
    }
}
if (!function_exists('isActiveRoute')) {
    function isActiveRoute($routes)
    {
        foreach ($routes as $route) {
            if (Route::is($route)) {
                return 'active';
            }
        }
        return '';
    }
}
?>
