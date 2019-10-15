<?php

class CLog extends SS_Object implements PermissionProvider {

    const EMERG   = Zend_Log::EMERG;    // 0: Emergency: system is unusable
    const ALERT   = Zend_Log::ALERT;    // 1: Alert: action must be taken immediately
    const CRIT    = Zend_Log::CRIT;     // 2: Critical: critical conditions
    const ERR     = Zend_Log::ERR;      // 3: Error: error conditions
    const WARN    = Zend_Log::WARN;     // 4: Warning: warning conditions
    const NOTICE  = Zend_Log::NOTICE;   // 5: Notice: normal but significant condition
    const INFO    = Zend_Log::INFO;     // 6: Informational: informational messages
    const DEBUG   = Zend_Log::DEBUG;    // 7: Debug: debug messages

    protected static $logger;

    protected static function get_logger() {
        if (!static::$logger) {
            $conf = SiteConfig::current_site_config();
            $cls = $conf->CLogBackend ?: 'SSLogBackend';
            static::$logger = new $cls;
        }
        return static::$logger;
    }

    public static function severity_name($code) {
        switch ($code) {
            case Zend_Log::EMERG:    // 0: Emergency: system is unusable
                return 'EMERG';
            case Zend_Log::ALERT:    // 1: Alert: action must be taken immediately
                return 'ALERT';
            case Zend_Log::CRIT:     // 2: Critical: critical conditions
                return 'CRIT';
            case Zend_Log::ERR:      // 3: Error: error conditions
                return 'ERR';
            case Zend_Log::WARN:     // 4: Warning: warning conditions
                return 'WARN';
            case Zend_Log::NOTICE:   // 5: Notice: normal but significant condition
                return 'NOTICE';
            case Zend_Log::INFO:     // 6: Informational: informational messages
                return 'INFO';
            case Zend_Log::DEBUG:    // 7: Debug: debug messages
                return 'DEBUG';
        }
    }

    /**
     * log something
     * @param  string $msg      [description]
     * @param  int $severity    [description]
     * @return [type]           [description]
     */
    public static function log($msg, $severity = self::ERR) {

        // should we log
        if (!static::get_logger()->shouldLog($severity)) return;

        // apparently so
        static::get_logger()->log($msg, $severity);
    }

    /**
     * create the permissions required
     * @return array
     */
    public function providePermissions() {
        return array(
            "ACCESS_CLOGS" => "Access Log Export Utility"
        );
    }
}
