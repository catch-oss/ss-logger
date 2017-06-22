<?php

class CLogConfExt extends DataExtension {

    private static $db = array(
        'CLogBackend' => 'Varchar(255)',
        'CLogLevel' => 'Varchar(255)',
    );

    private static $defaults = array(
        'CLogBackend' => 'SSLogBackend',
        'CLogLevel' => CLog::ERR,
    );

    public function updateCMSFields(FieldList $fields) {

        // classes
        $classes = ClassInfo::subclassesFor('CLogBackend');
        $flt = [];
        foreach ($classes as $k => $v) {
            if ($k != 'CLogBackend')
                $flt[$k] = $v;
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
