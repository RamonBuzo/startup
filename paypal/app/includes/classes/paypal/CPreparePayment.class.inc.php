<?php

require_once 'CPaypal.class.inc.php';

class CPreparePaypment extends CPayPal
{
    private static $instance = null;

    protected function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function start(Array $items, $settings = array(), $paymentMethod = 'paypal', $currency = 'USD')
    {
        ## unable to authenticate
        if (!$this->authenthication($settings['environment'])) {
            return false;
        }

        #set ApiContext
        $apiContext = PaypalContext::ApiContext($this->credentials, $this->sdkConfiguration);

        #set payment method
        $payer = PaymentMethod::ConfigPayment($paymentMethod);

        #set Items and Total to bill
        if (!$itemsList = Items::setItemsList($items, $currency)) {
            ## unable to load the items to sell.
            return false;
        }
        #set final amount.
        $amount = Amount::setAmount(Items::getTotal(), $currency);
        #set transaction.
        $transaction = Transaction::setTransaction($amount, $itemsList);
        #set redirections.
        $redirectUrls = Redirections::setURLs();

        self::_createPaymentURL($payer, $redirectUrls, $transaction, $apiContext);
    }

    private function _createPaymentURL(PayPal\Api\Payer $payer, PayPal\Api\RedirectUrls $redirectUrls, PayPal\Api\Transaction $transaction, PayPal\Rest\ApiContext $apiContext)
    {
        $payment = new PayPal\Api\Payment();
        $payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

        try {
            $payment->create($apiContext);
        } catch (Exception $e) {
            CLogger::getInstance()->add(__LINE__, __METHOD__, 'exception error', $e);
            die();
        }

        $url = $payment->getApprovalLink();
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'url paypal', $url);
        CLogger::getInstance()->getLog('PayPal starting checkout');
        header("Location: {$url}");
        exit;
    }
}