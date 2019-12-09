<?php

namespace CatchDesign\SSLogger;

use AbstractQueuedJob;
use QueuedJob;

use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;



/**
 * Queued job to clean up database and clean up miscellaneous files
 *
 */
class AssetLogCleanup extends AbstractQueuedJob implements QueuedJob {


    public function __construct($filePattern = null) {
        $this->currentStep = 0;
        $this->perStep = 100;
    }

    public function getTitle() {
        return 'File cleanup - cleaning with pattern ' . ($this->getFilePattern());
    }

    public function getFilePattern() {
        return '/.+\.log/';
    }

    public function getCLogDir() {
        return 'clog/';
    }

    public function getCLogPath() {
        return ASSETS_PATH . '/' . $this->getCLogDir();
    }

    /**
     * you get a runner for each signature
     * @return string
     */
    public function getSignature() {
        return md5(get_class($this) . $this->getCLogDir() . $this->getFilePattern());
    }

    /**
     * Indicate to the system which queue we think we should be in based
     * on how many objects we're going to touch on while processing.
     *
     * We want to make sure we also set how many steps we think we might need to take to
     * process everything - note that this does not need to be 100% accurate, but it's nice
     * to give a reasonable approximation
     *
     */
    public function getJobType() {
        $this->totalSteps = 100;
        return QueuedJob::QUEUED;
    }

    /**
     * This is called immediately before a job begins - it gives you a chance
     * to initialise job data and make sure everything's good to go
     *
     * What we're doing in our case is to queue up the list of items we know we need to
     * process still (it's not everything - just the ones we know at the moment)
     *
     * When we go through, we'll constantly add and remove from this queue, meaning
     * we never overload it with content
     */
    public function setup() {

        // what are we working with?
        $remainingChildren = [];

        // save these for later
        $this->remainingChildren = scandir($this->getCLogPath());

        $this->totalSteps = intval(count($this->remainingChildren) / $this->perStep);
    }

    /**
     * Lets process a single node, and publish it if necessary
     */
    public function process() {

        // debug output, but this is dumb because we are trying to clean up the files
        // $dbg = "Running a Filecleanup process\n";
        // $dbg.= "---------------\n";
        // CLog::log($dbg, CLog::DEBUG);

        // only process this many per run
        $process = $this->perStep;

        // TODO check if we miss some files if setup runs again
        $fileKey = $this->currentStep * $this->perStep;

        // get the remaining items in the job
        $remainingChildren = $this->remainingChildren;
        $remainingLength = count($remainingChildren);

        // loop through the files
        for ($fileKey; ($fileKey < $remainingLength) && $process; $fileKey++) {

            // the current file
            $file = $remainingChildren[$fileKey];

            // does it look like a file we want to delete
            if (preg_match('/' . $this->getFilePattern() . '/', $file)) {

                // debug output, but this is dumb because we are trying to clean up the files
                // CLog::log('Filecleanup deleting ' . $file, CLog::DEBUG);

                // delete the files
                unlink($this->getCLogDir() . '/' . $file);
                if ($iFile = DataObject::get_one(File::class, 'Filename = \'' . $this->getCLogDir() . '/' . $logFile . '\''))
                    $iFile->delete();

                // process tracker
                $process--;
            }
        }

        // if process hasn't hit 0, we have completed the job
        if ($process) {
            $this->completeJob();
            return;
        }

        // we need to always increment! This is important, because if we don't then our container
        // that executes around us thinks that the job has died, and will stop it running.
        $this->currentStep++;

    }

    /**
     * Outputs the completed file to the site's webroot
     */
    protected function completeJob() {

        $this->isComplete = true;
    }
}
