<?php
/***************************************************
 * Variable ENV
 * [MS_MODULES] => ex:PRODUCT,PAYMENT,DELIVERY
 * [MS_HOST_{MODULE}] => ex:"123.0.0.1,123.0.0.2,123.0.0.2"
 * [MS_PREFIX_{MODULE}] => ex: "/api"
 *
 *
 */

return [

    'prefix' => '/api',

    'prefix_module' => '/api',

    'modules' => env('MS_MODULES', null)

];
