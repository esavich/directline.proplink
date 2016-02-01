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
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB')

        );
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
        $html = '<input type="hidden"  name="' . $strHTMLControlName['VALUE'] . '"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '_HIDDEN" value="' . htmlentities(json_encode($value["VALUE"])) . '">';
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
        $value['VALUE'] = json_decode($value['VALUE'], true);
        return $value;
    }
}