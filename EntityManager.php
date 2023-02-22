<?php

namespace Bitrix\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Loader;
use Bitrix\Main\UserFieldLangTable;

class EntityManager
{
    protected $ufManager;
    protected $ufEnumsManager;
    protected $iblockPropertyManager;
    protected $iblockPropertyEnumsManager;

    public function __construct()
    {
        Loader::includeModule('highloadblock');
        $this->ufManager = new \CUserTypeEntity();
        $this->ufEnumsManager = new \CUserFieldEnum();
        $this->iblockPropertyManager = new \CIBlockProperty();
        $this->iblockPropertyEnumsManager = new \CIBlockPropertyEnum();
    }

    public function AddHighload($fields): AddResult
    {
        return HighloadBlockTable::add($fields);
    }

    public function addHighloads($data)
    {
        foreach ($data as $fields) {
            $this->AddHighload($fields);
        }
    }

    public function addUserField($fields): int
    {
        return $this->ufManager->Add($fields);
    }

    public function addUserFields($data)
    {
        foreach ($data as $fields) {
            $this->addUserField($fields);
        }
    }

    public function addUserFieldLang($fields)
    {
        return UserFieldLangTable::add($fields);
    }

    public function addUserFieldsLangs($data){
        foreach ($data as $fields) {
            $this->addUserFieldLang($fields);
        }
    }

    public function addUserFieldEnums($fieldId, $values){
        return $this->ufEnumsManager->SetEnumValues($fieldId, $values);
    }

    public function addUerFieldsEnums($data){
        $fields = [];

        foreach ($data as $row){
            $fields[$row['USER_FIELD_ID']][$row['ID']] = $row;
        }

        foreach ($fields as $fieldId => $values){
            $this->addUserFieldEnums($fieldId, $values);
        }
    }

    public function addIblockProperty($fields){
        return $this->iblockPropertyManager->Add($fields);
    }

    public function addIblockPropertyEmum($fields){
        return PropertyEnumerationTable::add($fields);
    }

    public function addIblockProperties($data){
        foreach ($data as $fields) {
            $this->addIblockProperty($fields);
        }
    }

    public function addIblockPropertiesEnums($data){
        foreach ($data as $fields) {
            $this->addIblockPropertyEmum($fields);
        }
    }
}