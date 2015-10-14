<?php
/**
 * User: Ram�n
 * Date: 10/9/2015
 * Time: 4:11 PM
 */

class CLogger
{
    private static $instance = null;
    private static $log = array();
    private static $mailingList = 'ramon@startup.com, real183@hotmail.com';

    protected function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add($line = '', $class = '', $key = '', $info = '')
    {
        self::$log[$line . '::' . $class][$key] = $info;
    }

    public function getLog($subject = '')
    {
        $this->_getGlobals();

        mail(self::$mailingList, 'CLogger Debug - ' . $subject .'-', print_r(self::$log,1), 'From: debugger@startup.com');

        return self::$log;
    }

    private function _getGlobals()
    {
        foreach ($_SERVER as $key => $value) {
            $key = strtolower(str_replace('HTTP_','',$key));
            self::$log['global_server'][$key] = $value;
        }

        foreach ($_GET as $key => $value) {
            self::$log['global_get'][$key] = $value;
        }

        foreach ($_POST as $key => $value) {
            self::$log['global_post'][$key] = $value;
        }

        foreach ($_COOKIE as $key => $value) {
            self::$log['global_cookie'][$key] = $value;
        }
    }
}