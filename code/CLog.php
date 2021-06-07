<?php

namespace CatchDesign\SSLogger;

use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Config\Configurable;
use CatchDesign\SSLogger\SSLogBackend;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Security\PermissionProvider;

class CLog implements PermissionProvider {

    use Extensible;
    use Injectable;
    use Configurable;

    const EMERG   = "EMERG";    // 0: Emergency: system is unusable
    const ALERT   = "ALERT";    // 1: Alert: action must be taken immediately
    const CRIT    = "CRIT";     // 2: Critical: critical conditions
    const ERR     = "ERR";      // 3: Error: error conditions
    const WARN    = "WARN";     // 4: Warning: warning conditions
    const NOTICE  = "NOTICE";   // 5: Notice: normal but significant condition
    const INFO    = "INFO";     // 6: Informational: informational messages
    const DEBUG   = "DEBUG";    // 7: Debug: debug messages

    const SEVERITY_MAP = [
        self::EMERG   => 0,
        self::ALERT   => 1,
        self::CRIT    => 2,
        self::ERR     => 3,
        self::WARN    => 4,
        self::NOTICE  => 5,
        self::INFO    => 6,
        self::DEBUG   => 7,
    ];

    protected static $logger;

    protected static function get_logger() {
        if (!static::$logger) {
            $conf = SiteConfig::current_site_config();
            $cls = $conf->CLogBackend ?: SSLogBackend::class;
            static::$logger = new $cls;
        }
        return static::$logger;
    }

    public static function severity_name($code) {
        switch ($code) {
            case self::EMERG:    // 0: Emergency: system is unusable
                return 'EMERG';
            case self::ALERT:    // 1: Alert: action must be taken immediately
                return 'ALERT';
            case self::CRIT:     // 2: Critical: critical conditions
                return 'CRIT';
            case self::ERR:      // 3: Error: error conditions
                return 'ERR';
            case self::WARN:     // 4: Warning: warning conditions
                return 'WARN';
            case self::NOTICE:   // 5: Notice: normal but significant condition
                return 'NOTICE';
            case self::INFO:     // 6: Informational: informational messages
                return 'INFO';
            case self::DEBUG:    // 7: Debug: debug messages
                return 'DEBUG';
            default:
                return $code;
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
