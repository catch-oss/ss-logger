<?php

class AssetLogBackend extends CLogBackend {

    public function log($msg, $severity = CLog::ERR) {

        // should we log
        if (!$this->shouldLog($severity)) return;

        // what file path are we looking at here
        $dir = 'clog';
        $fullPath = ASSETS_PATH . '/' . $dir;

        // make sure the dir exists
        if (!is_dir($fullPath))
            mkdir($fullPath, 0777, true);

        // make sure there's an htaccess file blocking access
        file_put_contents('Require all denied', ASSETS_PATH . '/' . $dir . '/.htaccess');

        // write the file
        file_put_contents($fullPath . '/' . $logFile, $msg, 'FILE_APPEND');
    }
}
