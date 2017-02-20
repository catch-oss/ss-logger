<?php

abstract class CLogBackend {

    /**
     *
     * @param  int $severity
     * @return boolean
     */
    protected function shouldLog($severity) {
        $conf = SiteConfig::current_site_config();
        return $conf->CLogLevel <= $severity;
    }

    abstract function log($msg, $severity = CLog::ERR);
}
