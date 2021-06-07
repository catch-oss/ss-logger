<?php
namespace CatchDesign\SSLogger;

use SilverStripe\SiteConfig\SiteConfig;

abstract class CLogBackend {

    /**
     *
     * @param  int $severity
     * @return boolean
     */
    public function shouldLog($severity = CLog::ERR) {
        $conf = SiteConfig::current_site_config();
        $refLvl = $conf->CLogLevel ?? CLog::ERR;
        $shouldLog = CLog::SEVERITY_MAP[$refLvl] >= CLog::SEVERITY_MAP[$severity];
        return $shouldLog;
    }

    abstract function log($msg, $severity = CLog::ERR);
}
