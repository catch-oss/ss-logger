<?php

class SSLogBackend extends CLogBackend {

    public function log($msg, $severity = CLog::ERR) {
        SS_Log::log($msg, $severity);
    }
}
