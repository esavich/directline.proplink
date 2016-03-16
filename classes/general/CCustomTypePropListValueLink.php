<?php
IncludeModuleLangFile(__FILE__);

class CCustomTypePropListValueLink
{


    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'propListValueLink',
            'DESCRIPTION' => GetMessage("DIRECTLINE_PROPLINK_PRIVAZKA_K_VARIANTU_SPISKA"),
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML')

        );
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value["VALUE"]) {
            if ($value['VALUE']) {
                $enumArr = CIBlockPropertyEnum::GetByID($value['VALUE']);
                $propObj = CIBlockProperty::GetByID($enumArr['PROPERTY_ID']);
                if ($propArr = $propObj->Fetch()) {
                    kint($enumArr, $propArr);
                    $printVal = $enumArr['VALUE'] . ' [' . $enumArr['ID'] . ']';
                }
            }

            $str = $propArr['NAME'] . ' [id: ' . $propArr['ID'] . '] (iblock: ' . $propArr['IBLOCK_ID'] . ') - ' . $printVal;
            return $str;
        } else {

            return '';
        }
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        global $APPLICATION;
        CJSCore::Init(array("jquery"));
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
        $APPLICATION->AddHeadScript('/bitrix/js/directline.proplink/script.js', true);
        $listPropObj = CIBlockProperty::GetList(array(), array('PROPERTY_TYPE' => 'L'));
        $allowedIblockList = array();
        while ($listPropArr = $listPropObj->Fetch()) {
            $allowedIblockList[] = $listPropArr['IBLOCK_ID'];
        }
        $allowedIblockList = array_unique($allowedIblockList);
        $iblocksObj = CIBlock::GetList(Array("NAME" => "ASC"), Array('ACTIVE' => 'Y', 'ID' => $allowedIblockList),
            true);
        $iblocks = array();
        $props = array();
        $currentIblock = false;

        $iblocksIdList = array();
        while ($iblockArr = $iblocksObj->Fetch()) {
            $iblocks[] = $iblockArr;
            $iblocksIdList[] = $iblockArr['ID'];
        }
        if ($value['VALUE']) {
            $enumArr = CIBlockPropertyEnum::GetByID($value['VALUE']);
            $propObj = CIBlockProperty::GetByID($enumArr['PROPERTY_ID']);
            while ($propArr = $propObj->Fetch()) {
                $currentIblock = $propArr['IBLOCK_ID'];
            }
        }
        $hValue = $value["VALUE"] ? htmlentities(\Bitrix\Main\Web\Json::encode($value["VALUE"])) : '';
        $html = '<input type="hidden"  name="' . $strHTMLControlName['VALUE'] . '"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '_HIDDEN" value="' . $hValue . '">';
        $html .= '<select data-type-filter="L" name="" class="' . $arProperty['USER_TYPE'] . '_IBLOCK"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '">';
        $html .= '<option disabled';
        if (!$currentIblock) {
            $html .= ' selected';
        }
        $html .= '>--</option>';
        foreach ($iblocks as $iblock) {
            $html .= '<option value="' . $iblock['ID'] . '"';
            if ($iblock['ID'] == $currentIblock) {
                $html .= ' selected ';
            }
            $html .= '>[' . $iblock['ID'] . '] ' . $iblock['NAME'] . ' (' . $iblock['CODE'] . ')</option>';
        }
        $html .= '</select>';

        $html .= '<select ';
        if ($value['VALUE'] && $enumArr) {
            $html .= 'data-initial-propid="' . $enumArr['PROPERTY_ID'] . '"';
            $html .= 'data-initial-value="' . $value['VALUE'] . '"';
        }
        $html .= '  class="' . $arProperty['USER_TYPE'] . '_PROP" id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '_PROPS">';

        $html .= '</select>';
        if ($arProperty['WITH_DESCRIPTION'] == 'Y') {
            $html .= '<br>';
            $html .= '<br>';
            $html .= GetMessage("DIRECTLINE_PROPLINK_DESCRIPTION");
            $html .= ' : ';
            $html .= '<input type="text" name="' . $strHTMLControlName['DESCRIPTION'] . '" value="' . $value['DESCRIPTION'] . '">';
        }
        $html .= '<hr>';

        return $html;

    }


}