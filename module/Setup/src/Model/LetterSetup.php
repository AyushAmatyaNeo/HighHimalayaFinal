<?php

namespace Setup\Model;

use Application\Model\Model;

class LetterSetup extends Model {

    const TABLE_NAME = "HRIS_LETTER_SETUP";
    const LETTER_SETUP_ID = "LETTER_SETUP_ID";
    const LETTER_TITLE = "LETTER_TITLE";
    const CREATED_DATE = "CREATED_DATE";
    const STATUS = "STATUS";
    const TOP_POSITION = "TOP_POSITION";
    const BOTTOM_POSITION = "BOTTOM_POSITION"; 
    const LEFT_POSITION = "LEFT_POSITION";
    const RIGHT_POSITION = "RIGHT_POSITION";
    const IS_CUSTOM = "IS_CUSTOM";
    const PARENT_ID = "PARENT_ID";

    public $letterSetupId;
    public $letterTitle;
    public $createdDate;
    public $status;
    public $topPosition;
    public $bottomPosition;
    public $leftPosition;
    public $rightPosition;
    public $isCustom;

    public $parent_id;

    public $mappings = [
        'letterSetupId' => self::LETTER_SETUP_ID,
        'letterTitle' => self::LETTER_TITLE,
        'createdDate' => self::CREATED_DATE,
        'status' => self::STATUS,
        'topPosition' => self::TOP_POSITION,
        'bottomPosition' => self::BOTTOM_POSITION,
        'leftPosition' => self::LEFT_POSITION,
        'rightPosition' => self::RIGHT_POSITION,
        'isCustom' => self::IS_CUSTOM,
        'parent_id' => self::PARENT_ID,

    ];

}

