<?php

namespace CatchDesign\SSLogger;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;

class AssetLogFileExt extends DataExtension {

    public function updateCMSFields(FieldList $fields) {

        // check if it's in the folder
        if (preg_match('/clog/', $this->owner->Filename)) {

            // update
            $fields->push(
                new LiteralField(
                    'FilePath',
                    '<div class="ui-tabs ui-tabs-panel">
                        <p class="field">
                            <a href="/AssetLogFileExporter/export/' . $this->owner->ID . '">Download Export File</a>
                        </p>
                    </div>'
                )
            );
        }
    }
}
