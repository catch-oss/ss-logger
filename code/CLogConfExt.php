<?php

namespace CatchDesign\SSLogger;

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
        foreach ($classes as $v) {
            if ($v != CLogBackend::class) {
                $flt[$v] = $v;
            }
        }

        // update
        $fields->addFieldsToTab(
            'Root.CLog',
            [
                new DropdownField(
                    'CLogBackend',
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
