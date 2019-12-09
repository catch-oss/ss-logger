<?php

namespace CatchDesign\SSLogger;

use CatchDesign\SSLogger\SSLogBackend;
use CatchDesign\SSLogger\CLogBackend;
use SilverStripe\Forms\FieldList;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;

class CLogConfExt extends DataExtension {

    private static $db = array(
        'CLogBackend' => 'Varchar(255)',
        'CLogLevel' => 'Varchar(255)',
    );

    private static $defaults = array(
        'CLogBackend' => SSLogBackend::class,
        'CLogLevel' => CLog::ERR,
    );

    public function updateCMSFields(FieldList $fields) {

        // classes
        $classes = ClassInfo::subclassesFor(CLogBackend::class);
        $flt = [];
        foreach ($classes as $k => $v) {
            if ($k != CLogBackend::class)
                $flt[$k] = $v;
        }

        // update
        $fields->addFieldsToTab(
            'Root.CLog',
            [
                new DropdownField(
                    CLogBackend::class,
                    'Logging Interface',
                    $flt
                ),
                new DropdownField(
                    'CLogLevel',
                    'Log Level',
                    [
                        CLog::ERR => 'ERR',
                        CLog::WARN => 'WARN',
                        CLog::NOTICE => 'NOTICE',
                        CLog::INFO => 'INFO',
                        CLog::DEBUG => 'DEBUG',
                    ]
                ),
            ]
        );
    }
}
