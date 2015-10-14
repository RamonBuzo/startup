<?php

class CProcess extends CPayPal
{
    private static $instance = null;

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function process($settings = array())
    {
        $usccess   = (isset($_GET['success'])   and !empty($_GET['success']))   ? $_GET['success'] : '';
        $paymentId = (isset($_GET['paymentId']) and !empty($_GET['paymentId'])) ? $_GET['paymentId'] : '';
        $token     = (isset($_GET['token'])     and !empty($_GET['token']))     ? $_GET['token']     : '';
        $payerId   = (isset($_GET['PayerID'])   and !empty($_GET['PayerID']))   ? $_GET['PayerID']   : '';

        ## unable to authenticate
        if (!$this->authenthication($settings['environment'])) {
            return false;
        }

        #set ApiContext
        $apiContext = PaypalContext::ApiContext($this->credentials, $this->sdkConfiguration);

        return Payment::execute($paymentId, $payerId, $apiContext);
    }
}