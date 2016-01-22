<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$resp = array();
CModule::IncludeModule("iblock");
if (!isset($_GET['iblockId'])) {

    $resp['SUCCESS'] = 'N';

} else {
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
}

echo json_encode($resp);