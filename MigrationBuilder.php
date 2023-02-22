<?php

namespace Bitrix\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\UserFieldLangTable;

class MigrationBuilder
{
    protected $configuration = [];

    public function __construct()
    {
        Loader::includeModule('highloadblock');
    }

    function buildFromDB()
    {
        $highloads = HighloadBlockTable::getList()->fetchAll();
        $userFieldsLang = UserFieldLangTable::getList()->fetchAll();
        $userFields = $this->collectResultValues(\CUserTypeEntity::GetList());
        $userFieldsEnums = $this->collectResultValues(\CUserFieldEnum::GetList());
        $iblockProperties = $this->collectResultValues(\CIBlockProperty::GetList());
        $iblockPropertiesEnums = $this->collectResultValues(\CIBlockPropertyEnum::GetList());

        return [
            'Highloads' => $highloads,
            'UserFields' => $userFields,
            'UserFieldsLang' => $userFieldsLang,
            'UserFieldsEnums' => $userFieldsEnums,
            'IblockProperties' => $iblockProperties,
            'IblockPropertiesEnums' => $iblockPropertiesEnums,
        ];
    }

    protected function collectResultValues($result){
        $items = [];
        while($item = $result->Fetch()){
            $items[] = $item;
        }
        return $items;
    }

    public function generateMigrationString(){
        $data = $this->buildFromDB();
        $result = '<?php'.PHP_EOL.'use Bitrix\Migration\EntityManager;'
            .PHP_EOL.PHP_EOL
            .'$manager = new EntityManager();'.PHP_EOL;

        foreach ($data as $dataKey => $dataItem){
            $result.= $this->buildData($dataKey, $dataItem);
        }

        return $result;
    }

    public function saveToFile($path){
        file_put_contents($path, $this->generateMigrationString());
    }

    protected function buildData($dataKey, $data){
        $methodName = 'build'.$dataKey.'Data';
        if(method_exists($this, $methodName)){
            return call_user_func(
                    [$this, $methodName],
                    $this->makeDataString($data)
                ).PHP_EOL.PHP_EOL;
        }
        return '';
    }

    protected function makeDataString($data){
        $export = var_export($data, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        return preg_replace(array_keys($patterns), array_values($patterns), $export);
    }

    protected function buildHighloadsData($dataString){
        return "\$manager->addHighloads({$dataString});";
    }

    protected function buildUserFieldsData($dataString){
        return "\$manager->addUserFields({$dataString});";
    }

    protected function buildUserFieldsLangData($dataString){
        return "\$manager->addUserFieldsLangs({$dataString});";
    }

    protected function buildUserFieldsEnumsData($dataString){
        return "\$manager->addUerFieldsEnums({$dataString});";
    }

    protected function buildIblockPropertiesData($dataString){
        return "\$manager->addIblockProperties({$dataString});";
    }

    protected function buildIblockPropertiesEnumsData($dataString){
        return "\$manager->addIblockPropertiesEnums({$dataString});";
    }
}