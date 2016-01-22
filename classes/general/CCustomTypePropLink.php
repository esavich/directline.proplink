<?php

class CCustomTypePropLink
{


    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'propLink',
            'DESCRIPTION' => GetMessage("DIRECTLINE_PROPLINK_PRIVAZKA_K_SVOYSTVU"),
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        global $APPLICATION;
        CJSCore::Init(array("jquery"));
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
        $APPLICATION->AddHeadScript('/bitrix/js/directline.proplink/script.js');
        $iblocksObj = CIBlock::GetList(Array("NAME" => "ASC"), Array('ACTIVE' => 'Y'), true);
        $iblocks = array();
        $props = array();
        $currentIblock = false;

        $iblocksIdList = array();
        while ($iblockArr = $iblocksObj->Fetch()) {
            $iblocks[] = $iblockArr;
            $iblocksIdList[] = $iblockArr['ID'];
        }
        if ($value['VALUE']) {
            $propObj = CIBlockProperty::GetByID($value['VALUE']);
            while ($propArr = $propObj->Fetch()) {
                $currentIblock = $propArr['IBLOCK_ID'];
            }
        }

        $html = '<select name="" class="' . $arProperty['USER_TYPE'] . '_IBLOCK"  id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '">';
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

        $html .= '<select name="' . $strHTMLControlName['VALUE'] . '"';
        if ($value['VALUE']) {
            $html .= 'data-initial-value="' . $value['VALUE'] . '"';
        }
        $html .= '  class="' . $arProperty['USER_TYPE'] . '_PROP" id="IBLOCK_' . md5($strHTMLControlName['VALUE']) . '_PROPS">';

        $html .= '</select>';

        $html .= '<hr>';

        return $html;

    }
}