<?php

class PaypalContext
{
    public static function ApiContext($credentials, Array $settings)
    {
        $apiContext = new PayPal\Rest\ApiContext($credentials, 'Request' . time());

        self::_setConfig($apiContext, $settings);

        #Logger
        //CLogger::getInstance()->add(__LINE__, __METHOD__, 'credentials' , self::_getCredential($apiContext));
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'settings' , self::_getConfig($apiContext));
        return $apiContext;
    }

    private static function _setConfig(PayPal\Rest\ApiContext &$apiContext, $settings)
    {
        $apiContext->setConfig($settings);
    }

    private static function _getConfig(PayPal\Rest\ApiContext &$apiContext)
    {
        return $apiContext->getConfig();
    }

    private static function _getCredential(PayPal\Rest\ApiContext &$apiContext)
    {
        return $apiContext->getCredential();
    }
}

class PaymentMethod
{
    public static function ConfigPayment($method = 'paypal')
    {
        $payer = new PayPal\Api\Payer();

        self::_setPaymentMethod($payer, $method);

        #Logger
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'payment method' , self::_getPaymentMethod($payer));
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'payer info' , self::_getPayerInfo($payer));
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'status' , self::_getStatus($payer));

        return $payer;
    }

    private static function _setPaymentMethod(PayPal\Api\Payer &$payer, $method)
    {
        $payer->setPaymentMethod($method);
    }

    private static function _getPaymentMethod(PayPal\Api\Payer &$payer)
    {
        return $payer->getPaymentMethod();
    }

    private static function _getPayerInfo(PayPal\Api\Payer &$payer)
    {
        return $payer->getPayerInfo();
    }

    private static function _getStatus(PayPal\Api\Payer &$payer)
    {
        return $payer->getStatus();
    }
}

class Items
{
    private static $total = 0;

    public static function setItemsList(Array $items, $currency = 'USD')
    {
        $list = self::_setItemsList($items, $currency);

        #Logger
        CLogger::getInstance()->add(__LINE__, __METHOD__, 'items list', $list);

        if (!is_array($list) or empty($list)) {
            return false;
        }

        $itemList = new PayPal\Api\ItemList();
        $itemList->setItems($list);
        return $itemList;
    }


    private static function _setItemsList($items = array(), $currency = 'USD')
    {
        if (!is_array($items) or empty($items)) {
            return false;
        }
        $itemsList = array();

        foreach ($items as $key => $item) {
            if (empty($item['price']) or !is_numeric($item['price'])) {
                return false;
            }
            $item['currency'] = $currency;
            $item['quantity'] = 1;
            $itemsList[] = Item::addItem($item);
            self::$total = self::$total + $item['price'];
        }
        return $itemsList;
    }

    public static function getTotal()
    {
        return self::$total;
    }
}

class Item
{
    public static function addItem($product = array())
    {
        if (empty($product) or !is_array($product)) {
            return false;
        }

        $item = new PayPal\Api\Item();

        self::_setname($item, $product['name']);
        self::_setPrice($item, $product['price']);
        self::_setCurrency($item, $product['currency']);
        self::_setQuantity($item, $product['quantity']);
        self::_setDescription($item, $product['descr']);
        return $item;
    }

    private static function _setname(PayPal\Api\Item &$item, $product_name = '')
    {
        $product_name = (!empty($product_name)) ? $product_name : 'product not defined';
        $item->setName($product_name);
    }

    private static function _setPrice(PayPal\Api\Item &$item, $price = 0)
    {
        $price = (!empty($price)) ? $price : 0;
        $item->setPrice($price);
    }

    private static function _setCurrency(PayPal\Api\Item &$item, $currency = 'USD')
    {
        $currency = (!empty($currency)) ? $currency : 'USD';
        $item->setCurrency($currency);
    }

    private static function _setQuantity(PayPal\Api\Item &$item, $quantity = 1)
    {
        $quantity = (!empty($quantity)) ? $quantity : 1;
        $item->setQuantity($quantity);
    }

    private static function _setDescription(PayPal\Api\Item &$item, $description = '')
    {
        $description = (!empty($description)) ? $description : 1;
        $item->setDescription($description);
    }
}

class Amount
{
    public static function setAmount($total = 0, $currency = 'USD')
    {
        if (empty($total)) {
            return false;
        }

        if (empty($currency)) {
            return false;
        }

        $amount = new PayPal\Api\Amount();

        self::_setCurrency($amount, $currency);
        self::_setTotal($amount, $total);
        return $amount;
    }

    private static function _setCurrency(PayPal\Api\Amount &$amount, $currency = 'USD')
    {
        $amount->setCurrency($currency);
    }

    private static function _setTotal(PayPal\Api\Amount &$amount, $total = 0)
    {
        $amount->setTotal($total);
    }
}

class Transaction
{
    public static function setTransaction(PayPal\Api\Amount $amount, PayPal\Api\ItemList $itemList)
    {
        $transaction = new PayPal\Api\Transaction();

        self::_setAmount($transaction, $amount);
        self::_setItemList($transaction, $itemList);
        self::_setDescription($transaction, 'Pay For something');
        self::_setInvoiceNumber($transaction);
        return $transaction;
    }

    private static function _setDescription(PayPal\Api\Transaction &$transaction, $description = 'Pay For something')
    {
        $transaction->setDescription($description);
    }

    private static function _setAmount(PayPal\Api\Transaction &$transaction, PayPal\Api\Amount $amount)
    {
        $transaction->setAmount($amount);
    }

    private static function _setItemList(PayPal\Api\Transaction &$transaction, PayPal\Api\ItemList $itemList)
    {
        $transaction->setItemList($itemList);
    }

    private static function _setInvoiceNumber(PayPal\Api\Transaction &$transaction)
    {
        $transaction->setInvoiceNumber(uniqid());
    }
}

class Redirections
{
    public static function setURLs($urls = array())
    {
        $redirectUrls = new PayPal\Api\RedirectUrls();

        $return_rul = (isset($urls['return_url']) and !empty($urls['return_url'])) ? $urls['return_url'] : 'http://localhost/paypal/default_url_return.php?approved=true';
        $cancel_rul = (isset($urls['cancel_url']) and !empty($urls['cancel_url'])) ? $urls['cancel_url'] : 'http://localhost/paypal/cancel_url_return.php?approved=false';

        self::_setReturnUrl($redirectUrls, $return_rul);
        self::_setCancelUrl($redirectUrls, $cancel_rul);

        #Logger
        CLogger::getInstance()->add(__LINE__, __METHOD__,'return url', self::_getReturnUrl($redirectUrls));
        CLogger::getInstance()->add(__LINE__, __METHOD__,'cancel url', self::_getCancelUrl($redirectUrls));

        return $redirectUrls;
    }

    private static function _setReturnUrl(PayPal\Api\RedirectUrls &$redirectUrls, $url)
    {
        $redirectUrls->setReturnUrl($url);
    }

    private static function _setCancelUrl(PayPal\Api\RedirectUrls &$redirectUrls, $url)
    {
        $redirectUrls->setCancelUrl($url);
    }

    private static function _getReturnUrl(PayPal\Api\RedirectUrls &$redirectUrls)
    {
        return $redirectUrls->getReturnUrl();
    }

    private static function _getCancelUrl(PayPal\Api\RedirectUrls &$redirectUrls)
    {
        return $redirectUrls->getCancelUrl();
    }
}

class Payment
{
    public static function execute($paymentId, $payerId, PayPal\Rest\ApiContext $apiContext)
    {
        #Logger
        CLogger::getInstance()->add(__LINE__, __CLASS__, 'payment_id', $paymentId);
        CLogger::getInstance()->add(__LINE__, __CLASS__, 'payer_id', $payerId);

        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
        self::_setIndent($payment);
        $execution = PaymentExecution::execute($payerId);
        return self::_process($payment, $apiContext, $execution);

    }

    private static function _process(\PayPal\Api\Payment &$payment, PayPal\Rest\ApiContext &$apiContext, PayPal\Api\PaymentExecution &$execution)
    {
        #Logger
        CLogger::getInstance()->add(__LINE__, __CLASS__, 'status_before_to_process', self::_getStatus($payment));
        try {
            $result = $payment->execute($execution, $apiContext);
            CLogger::getInstance()->add(__LINE__, __CLASS__, 'result', $result);
            $payment_success = true;
        } catch(Exception $e) {
            $data = json_decode($e->getData());
            $payment_success = array(
                'code'      => $e->getCode(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'exception' => $data->name,
                'reason'    => self::_getFailureReason($payment)
            );
        }
        #Logger
        CLogger::getInstance()->add(__LINE__, __CLASS__, 'payment_status', $payment_success);
        CLogger::getInstance()->add(__LINE__, __CLASS__, 'status_after_to_process', self::_getStatus($payment));
        return $payment_success;
    }


    private static function _setIndent(\PayPal\Api\Payment &$payment)
    {
        $payment->setIntent('sale');
    }

    private static function _getFailureReason(\PayPal\Api\Payment &$payment)
    {
        return $payment->getFailureReason();
    }

    private static function _getStatus(\PayPal\Api\Payment &$payment)
    {
        return $payment->getState();
    }
}

class PaymentExecution
{
    public static function execute($payerId)
    {
        $execution = new PayPal\Api\PaymentExecution();
        self::_setPayerIdn($execution, $payerId);
        return $execution;
    }

    private static function _setPayerIdn(\PayPal\Api\PaymentExecution &$execution, $payerId)
    {
        return $execution->setPayerId($payerId);
    }
}