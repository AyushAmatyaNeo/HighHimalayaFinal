<?php

namespace Setup\Model;

use Application\Model\Model;

class LetterSetupDetail extends Model {
    const TABLE_NAME = "HRIS_LETTER_SETUP_DETAIL";

    const LETTER_SETUP_DETAIL_ID = "LETTER_SETUP_DETAIL_ID";
    const LETTER_SETUP_ID = "LETTER_SETUP_ID";
    const DESCRIPTION = "DESCRIPTION";

    public $letterSetupDetailId;
    public $letterSetupId;
    public $description;

    public $mappings = [
        'letterSetupDetailId' => self::LETTER_SETUP_DETAIL_ID,
        'letterSetupId' => self::LETTER_SETUP_ID,
        'description' => self::DESCRIPTION,
    ];

}

