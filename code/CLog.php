<?php

class CLog extends Object implements PermissionProvider {

    const EMERG   = Zend_Log::EMERG;    // 0: Emergency: system is unusable
    const ALERT   = Zend_Log::ALERT;    // 1: Alert: action must be taken immediately
    const CRIT    = Zend_Log::CRIT;     // 2: Critical: critical conditions
    const ERR     = Zend_Log::ERR;      // 3: Error: error conditions
    const WARN    = Zend_Log::WARN;     // 4: Warning: warning conditions
    const NOTICE  = Zend_Log::NOTICE;   // 5: Notice: normal but significant condition
    const INFO    = Zend_Log::INFO;     // 6: Informational: informational messages
    const DEBUG   = Zend_Log::DEBUG;    // 7: Debug: debug messages

    protected static $logger;

    protected static function getLogger() {
        if (!static::$logger) {
            $conf = SiteConfig::current_site_config();
            $cls = $conf->CLogBackend;
            static::$logger = new $cls;
        }
        return static::$logger;
    }

    /**
     * log something
     * @param  string $msg      [description]
     * @param  int $severity    [description]
     * @return [type]           [description]
     */
    public static function log($msg, $severity = self::ERR) {
        static::getLogger()->log($msg, $severity);
    }

    /**
     * create the permissions required
     * @return array
     */
    public function providePermissions() {
        return array(
            "ACCESS_LOGS" => "Access Data Object Export Utility"
        );
    }
}
