<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$resp = array();
CModule::IncludeModule("iblock");
if (!isset($_GET['iblockId']) && !isset($_GET['propId'])) {
    $resp['SUCCESS'] = 'N';

} else {
    if ($_GET['iblockId']) {
        $IBLOCK_ID = (int)$_GET['iblockId'];
        $iblock = CIBlock::GetByID($IBLOCK_ID);
        if ($iblock->SelectedRowsCount() > 0) {
            $propObj = CIBlockProperty::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $IBLOCK_ID));
            while ($propArr = $propObj->Fetch()) {
                $resp["PROPS"][] = array(
                    'ID' => $propArr['ID'],
                    'NAME' => $propArr['NAME'],
                    'CODE' => $propArr['CODE'],
                );
            }
            $resp['SUCCESS'] = 'Y';
        } else {
            $resp['SUCCESS'] = 'N';
        }
    } elseif ($_GET['propId']) {
        $PROP_ID = (int)$_GET['propId'];
        $propObj = CIBlockProperty::GetByID($PROP_ID);
        if ($propArr = $propObj->Fetch()) {
            $resp['SUCCESS'] = 'Y';
            $resp['PROPERTY_TYPE'] = $propArr['PROPERTY_TYPE'];
            if ($propArr['PROPERTY_TYPE'] == 'L') {
                $enumObj = CIBlockPropertyEnum::GetList(
                    array("SORT" => "ASC", "VALUE" => "ASC"),
                    array("PROPERTY_ID" => $PROP_ID)
                );
                while($enumArr = $enumObj->Fetch()) {
                    $resp['LIST'][$enumArr['ID']] = $enumArr["VALUE"];
                }
            }
        } else {
            $resp['SUCCESS'] = 'N';
        }
    }

}

echo json_encode($resp);