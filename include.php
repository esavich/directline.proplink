<?php

$arClassesList = array(
    "CCustomTypePropLink" => "classes/general/CCustomTypePropLink.php"
);

$MODULE_ID = basename(dirname(__FILE__));

if (method_exists(CModule, "AddAutoloadClasses")) {
    CModule::AddAutoloadClasses($MODULE_ID, $arClassesList);
} else {
    foreach ($arClassesList as $sClassName => $sClassFile) {
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/{$MODULE_ID}/{$sClassFile}");
    }
}
?>