<?php

namespace Bitrix\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Application;
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

    protected function checkSkipSection($sectionName)
    {
        return
            (is_array($GLOBALS['CONCRETE_MIGRATIONS']) && !in_array($sectionName, $GLOBALS['CONCRETE_MIGRATIONS'])) ||
            (is_array($GLOBALS['SKIP_MIGRATION']) && in_array($sectionName, $GLOBALS['SKIP_MIGRATION']));
    }

    public function AddHighload($fields): AddResult
    {
        return HighloadBlockTable::add($fields);
    }

    public function addHighloads($data)
    {
        if ($this->checkSkipSection('highloads')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate highloads");
        $total = count($data);
        foreach ($data as $key => $fields) {
            $logger->showProgress($key + 1, $total);
            $this->AddHighload($fields);
        }
        $logger->close();
    }

    public function addUserField($fields): int
    {
        return $this->ufManager->Add($fields);
    }

    public function addUserFields($data)
    {
        if ($this->checkSkipSection('userfields')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate user-fields");
        $total = count($data);
        foreach ($data as $key => $fields) {
            $logger->showProgress($key + 1, $total);
            $this->addUserField($fields);
        }
        $logger->close();
    }

    public function addUserFieldLang($fields)
    {
        return UserFieldLangTable::add($fields);
    }

    public function addUserFieldsLangs($data)
    {
        if ($this->checkSkipSection('userfield-lang')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate user-field lang");
        $total = count($data);
        foreach ($data as $key => $fields) {
            try {
                $this->addUserFieldLang($fields);
                $logger->showProgress($key + 1, $total);
            } catch (\Exception $exception) {
                $logger->showError($exception->getMessage());
            }
        }
        $logger->close();
    }

    public function addUserFieldEnums($fieldId, $values)
    {
        return $this->ufEnumsManager->SetEnumValues($fieldId, $values);
    }

    public function addUerFieldsEnums($data)
    {
        if ($this->checkSkipSection('userfield-enums')) {
            return;
        }
        $fields = [];

        foreach ($data as $row) {
            $fields[$row['USER_FIELD_ID']][$row['ID']] = $row;
        }

        $logger = new \MigrationLogger("Migrate user-field enums");
        $total = count($fields);
        $num = 1;
        foreach ($fields as $fieldId => $values) {
            $logger->showProgress($num++, $total);
            $this->addUserFieldEnums($fieldId, $values);
        }
        $logger->close();
    }

    public function addIblockProperty($fields)
    {
        return $this->iblockPropertyManager->Add($fields);
    }

    public function addIblockPropertyEmum($fields)
    {
        return PropertyEnumerationTable::add($fields);
    }

    public function addIblockProperties($data)
    {
        if ($this->checkSkipSection('iblock-properties')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate iblock properies");
        $total = count($data);
        foreach ($data as $key => $fields) {
            $logger->showProgress($key + 1, $total);
            $this->addIblockProperty($fields);
        }
        $logger->close();
    }

    public function rawSql($data){
        if ($this->checkSkipSection('rawsql')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate tables");
        $connection = Application::getConnection();
        foreach ($data as $key => $query){
            try {
                $logger->showProgress($key + 1, count($data));
                $connection->query($query);
            }catch (\Exception $exception){
                $logger->showError($exception->getMessage());
            }
        }
        $logger->close();
    }

    public function addIblockPropertiesEnums($data)
    {
        if ($this->checkSkipSection('iblock-property-enums')) {
            return;
        }
        $logger = new \MigrationLogger("Migrate iblock property enums");
        $total = count($data);
        foreach ($data as $key => $fields) {
            $logger->showProgress($key + 1, $total);
            $this->addIblockPropertyEmum($fields);
        }
        $logger->close();
    }
}