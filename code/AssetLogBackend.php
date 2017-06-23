<?php

class AssetLogBackend extends CLogBackend {

    // -------------
    // configuration
    // -------------

    /**
     * @config
     */
    protected static $conf = [];

    /**
     * default config fallbacks
     * @var array
     */
    protected static $defaults = [
        'maxLogSize' => 100000000,
    ];

    /**
     *  @param  array|object $conf An associative array containing the configuration - see self::$conf for an example
     *  @return void
     */
    public static function set_conf($conf) {
        $conf = (array) $conf;
        static::$conf = static::array_merge_recursive_distinct(static::$conf, $conf);
    }

    /**
     *  @return stdClass
     */
    public static function get_conf() {
        return (object) static::array_merge_recursive_distinct(static::$defaults, static::$conf);
    }

    /**
     * @return void
     */
    protected static function set_conf_from_yaml() {
        $conf = (array) Config::inst()->get(get_called_class(), 'conf');
        if (!empty($conf))
            static::$conf = static::array_merge_recursive_distinct(static::$conf, $conf);
    }

    /**
     *  @return void
     */
    protected function configure() {

        // configure from YAML if available
        static::set_conf_from_yaml();
    }

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->configure();
    }

    /**
     *  @param  array $array1 The first array
     *  @param  array $array2 The second array
     *  @return array the merged array
     */
    public static function array_merge_recursive_distinct(array $array1, array $array2) {

        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    public function log($msg, $severity = CLog::ERR) {

        // obtain the conf
        $conf = static::get_conf();

        // what file path are we looking at here
        $dir = 'clog';
        $fullPath = ASSETS_PATH . '/' . $dir;

        // init some path vars
        $dirRelPath = 'assets/' . $dir . '/';

        // ensure we have folder record
        if (!$folder = DataObject::get_one('Folder', 'Filename = \'' . $dirRelPath . '\'')) {

            // make sure the dir exists
            if (!is_dir($fullPath))
                mkdir($fullPath, 0777, true);

            // create record
            $folder = Folder::create()->update([
                'Name' => $dir,
                'Title' => $dir,
                'Filename' => $dirRelPath,
                'ParentID' => 0,
                'OwnerID' => 1,
            ]);

            // did it work
            $folder->write();
        }

        // make sure there's an htaccess file blocking access
        file_put_contents(ASSETS_PATH . '/' . $dir . '/.htaccess', 'Require all denied');

        // generate the path
        $logFile = CLog::severity_name($severity) . '.log';
        $file = $fullPath . '/' . $logFile;

        // check file size of log
        if (is_file($file)) {
            if (filesize($file) >= $conf->maxLogSize) {
                $i = 1;
                while (is_file($file . '.' . $i)) {
                    $i++;
                }
                rename($file, $file . '.' . $i);
            }
        }

        // ensure we have a file record
        if (!$iFile = DataObject::get_one('File', 'Filename = \'' . $dirRelPath . $logFile . '\'')) {

            // create record
            $iFile = File::create()->update([
                'Name' => $file,
                'Title' => $file,
                'Filename' => $dirRelPath . $logFile,
                'ParentID' => $folder->ID,
                'OwnerID' => 1,
            ]);

            // did it work
            $iFile->write();
        }

        // write the file
        file_put_contents($file, $msg, FILE_APPEND);
    }
}
