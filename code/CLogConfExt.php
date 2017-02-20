<?php

class CLogConfExt extends DataExtension {

    private static $db = array(
        'CLogBackend' => 'Varchar(255)',
        'CLogLevel' => 'Boolean',
    );

    private static $defaults = array(
        'CLogLevel' => CLog::ERR,
        'CLogBackend' => 'SSLogBackend'
    );

    public function updateCMSFields($fields) {
        $fields->addFieldsToTab(
            'Root.CLog',
            [
                new DropdownField(
                    'CLogBackend',
                    'Logging Interface',
                    ClassInfo::subclassesFor('CLogBackend')
                ),
                new DropdownField(
                    'CLogLevel',
                    'Log Level',
                    [
                        'ERR' => CLog::ERR,
                        'WARN' => CLog::WARN,
                        'NOTICE' => CLog::NOTICE,
                        'INFO' => CLog::INFO,
                        'DEBUG' => CLog::DEBUG
                    ]
                ),
            ]
        );
    }
}
