<?php

namespace Setup\Model;

use Application\Model\Model;

class LetterHead extends Model {

    const TABLE_NAME = "HRIS_LETTER_HEAD";
    const FILE_CODE = "FILE_CODE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const FILE_PATH = "FILE_PATH";
    const FILE_NAME = "FILE_NAME";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $fileCode;
    public $employeeId;
    public $filePath;
    public $fileName;
    public $createdDt;
    public $modifiedDt;
    
    public $mappings = [
        'fileCode' => self::FILE_CODE,
        'employeeId' => self::EMPLOYEE_ID,
        'filePath' => self::FILE_PATH,
        'fileName' => self::FILE_NAME,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
    ];

}
