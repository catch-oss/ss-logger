<?php

namespace CatchDesign\SSLogger;

use SS_Log;


/**
 * SS_Log will drop log attempts log debug level events in prod etc
 */
class SSLogBackend extends CLogBackend {

    public function log($msg, $severity = CLog::ERR) {
        SS_Log::log($msg, $severity);
    }
}
