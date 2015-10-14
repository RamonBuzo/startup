<?php

require_once 'app/includes/header.inc.php';

$items = array(
    array(
        'name' => 'apuesta',
        'price' => 8000,
        'descr' => 'descripcion corta'
    ),
    /*
    array(
        'name' => 'apuesta',
        'price' => 700,
        'descr' => 'descripcion corta'
    ),
    */
);

CPreparePaypment::getInstance()->start($items, $settings['billing']['paypal']);