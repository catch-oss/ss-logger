<?php

namespace CatchDesign\SSLogger;

use SilverStripe\Control\HTTP;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\Assets\File;
use SilverStripe\Control\Controller;

class AssetLogFileExporter extends Controller {

    private static $allowed_actions = array(
        'index',
        'export'
    );

    public function init() {

        // make sure this doesn't get cached
        HTTP::set_cache_age(0);

        // call init on the parent
        parent::init();

        // security check
        if (!Permission::check('ACCESS_CLOGS')) Security::permissionFailure();
    }

    public function export() {

        // get the ID
        $id = (int) $this->request->param('ID');

        // find the export
        if (!$file = File::get()->filter(['ID' => $id])->first()) {
            $this->httpError(404);
            exit;
        }

        // generate the file path
        $filename = str_replace('assets/assets', 'assets', ASSETS_PATH . '/' . $file->Filename);

        // is it a file
        if (!is_file($filename)) {
            $this->httpError(404);
            exit;
        }

        // everything is JSON
        $this->response->addHeader('Content-Type', 'text/plain');
        return file_get_contents($filename);
    }
}
