<?php
IncludeModuleLangFile(__FILE__);

class CCustomTypePropValueLink
{


    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'propValueLink',
            'DESCRIPTION' => GetMessage("DIRECTLINE_PROPLINK_PRIVAZKA_K_ZNACHENIU_SVOYSTVU"),
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
            'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML')

        );
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value["VALUE"]) {
            $propArr = CIBlockProperty::GetByID($value['VALUE']['PROPERTY_ID'])->Fetch();
            if ($propArr['PROPERTY_TYPE'] == 'L') {
                $enumObj = CIBlockPropertyEnum::GetList(
                    array("SORT" => "ASC", "VALUE" => "ASC"),
                    array("PROPERTY_ID" => $PROP_ID, 'ID' => $value['VALUE']['VALUE'])
                );
                while ($enumArr = $enumObj->Fetch()) {
                    $printVal = $enumArr["VALUE"] . ' [' . $enumArr['ID'] . ']';
                }
            } elseif ($propArr['PROPERTY_TYPE'] == 'S' && $propArr['USER_TYPE'] == 'directory') {

                $extendedValue = CIBlockPropertyDirectory::GetExtendedValue($propArr,
                    array("VALUE" => $value['VALUE']['VALUE']));
                $printVal = $extendedValue['VALUE'] . ' [' . $value['VALUE']['VALUE'] . ']';

            } else {
                $printVal = $value['VALUE']['VALUE'];
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
        $iblocksObj = CIBlock::GetList(Array("NAME" => "ASC"), Array('ACTIVE' => 'Y'), true);
        $iblocks = array();
        $props = array();
        $currentIblock = false;

        $iblocksIdList = array();
        while ($iblockArr = $iblocksObj->Fetch()) {
            $iblocks[] = $iblockArr;
            $iblocksIdList[] = $iblockArr['ID'];
        }
        if ($value['VALUE']["PROPERTY_ID"]) {
            $propObj = CIBlockProperty::GetByID($value['VALUE']["PROPERTY_ID"]);
            while ($propArr = $propObj->Fetch()) {
                $currentIblock = $propArr['IBLOCK_ID'];
            }
        }
        $hValue = $value["VALUE"] ? htmlentities(\Bitrix\Main\Web\Json::encode($value["VALUE"])) : '';
        $html = '<input type="hidden"  name="' . $strHTMLControlName['VALUE'] . '"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '_HIDDEN" value="' . $hValue . '">';
        $html .= '<select name="" class="' . $arProperty['USER_TYPE'] . '_IBLOCK"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '">';
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
        if ($value['VALUE']) {
            $html .= 'data-initial-propid="' . $value['VALUE']['PROPERTY_ID'] . '"';
            $html .= 'data-initial-value="' . $value['VALUE']['VALUE'] . '"';
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

    function ConvertToDB($arProperty, $value)
    {
        return $value;
    }

    function ConvertFromDB($arProperty, $value)
    {
        if ($value['VALUE']) {
            $value['VALUE'] = \Bitrix\Main\Web\Json::decode($value['VALUE'], true);
        }
        return $value;
    }
}