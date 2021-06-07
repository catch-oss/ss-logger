<?php

namespace CatchDesign\SSLogger;

use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

/**
 * SS_Log will drop log attempts log debug level events in prod etc
 */
class SSLogBackend extends CLogBackend {

    public function log($msg, $severity = CLog::ERR) {
        $logger = Injector::inst()->get(LoggerInterface::class);
        switch ($severity) {
            case CLog::EMERG:    // 0: Emergency: system is unusable
                $logger->emergency($msg);
            case CLog::ALERT:    // 1: Alert: action must be taken immediately
                $logger->alert($msg);
            case CLog::CRIT:     // 2: Critical: critical conditions
                $logger->critical($msg);
            case CLog::ERR:      // 3: Error: error conditions
                $logger->error($msg);
            case CLog::WARN:     // 4: Warning: warning conditions
                $logger->warning($msg);
            case CLog::NOTICE:   // 5: Notice: normal but significant condition
                $logger->notice($msg);
            case CLog::INFO:     // 6: Informational: informational messages
                $logger->info($msg);
            case CLog::DEBUG:    // 7: Debug: debug messages
                $logger->debug($msg);
            default:
                $logger->log(CLog::NOTICE, $msg);
        }
    }
}
