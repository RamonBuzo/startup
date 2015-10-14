<?php
/**
 * Created by PhpStorm.
 * User: Ram�n
 * Date: 10/8/2015
 * Time: 11:19 PM
 */
require __DIR__ . '/PayPal-PHP-SDK/vendor/autoload.php';
require __DIR__ . '/../CLogger.class.inc.php';
require_once 'CBasePaypal.class.inc.php';

class CPayPal
{
    private static $client_id = 'AQhrISxd1GvWftneHgWU-1YMks0VcxTtDtg6W0lCQ9O6Ec5WhGvUH2fN4vAFOdTuf-FycYDiImakRaQC';
    private static $client_secret = 'EMtvJcJ4t2pCicQh5tJlxdmiYiuA2PsCtP3CdSTgJCw17PLnDZJS43BUFsBLG1_wQweCPSSXieWdZiPQ';
    protected $credentials = null;
    protected $sdkConfiguration = array();

    protected function authenthication($environment = 'production')
    {
        $this->setEnvironment($environment);
        $this->credentials = new PayPal\Auth\OAuthTokenCredential(self::$client_id, self::$client_secret);

        if (!$this->getAccessToken()) {
            return false;
        }
        return true;
    }

    protected function setEnvironment($environment = 'production')
    {
        $this->sdkConfiguration['mode'] = 'paypal';

        if (strcasecmp($environment, 'sandbox') == 0) {
            $this->sdkConfiguration['mode'] = 'sandbox';
        }
    }

    protected function getAccessToken()
    {
        return (!is_null($this->credentials->getAccessToken($this->sdkConfiguration))) ? true : false;
    }
}