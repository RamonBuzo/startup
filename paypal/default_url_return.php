<?php

require_once 'app/includes/header.inc.php';

$result = CProcess::getInstance()->process($settings['billing']['paypal']);
CLogger::getInstance()->getLog('PayPal Billing Result');