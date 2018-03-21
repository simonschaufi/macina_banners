<?php
defined('TYPO3_MODE') or die();

/* Set up the tt_content fields for the frontend plugins */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['macina_banners_pi1'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['macina_banners_pi1'] = 'tx_macinabanners_placement;;;;1-1-1, tx_macinabanners_mode';

/* Add the plugins */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin([
    'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
    'macina_banners_pi1'
], 'list_type', 'macina_banners');

/* Add the flexforms to the TCA */
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('macina_banners_pi1', 'FILE:EXT:macina_banners/Configuration/FlexForms/Pi1.xml');

//medialights: configure the renderMode placement
$macinaCfg = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['macina_banners']);
$renderType = 'selectMultipleSideBySide';
if ($macinaCfg['renderMode'] == 'singlebox' || $macinaCfg['renderMode'] == 'checkbox') {
    $renderType = $macinaCfg['renderMode'];
}
unset ($macinaCfg);

$tempColumns = [
    'tx_macinabanners_placement' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_placement',
        'config' => [
            'type' => 'select',
            'itemsProcFunc' => \JBartels\MacinaBanners\BackendHelper\Placement::class . '->main',
            'renderType' => $renderType,
            'size' => 5,
            'maxitems' => 50,
        ]
    ],
    'tx_macinabanners_mode' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_mode',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_mode.I.0',
                    'all',
                    'EXT:macina_banners/Resources/Public/Images/selicon_tt_content_tx_macinabanners_mode_0.gif'
                ],
                [
                    'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_mode.I.1',
                    'random',
                    'EXT:macina_banners/Resources/Public/Images/selicon_tt_content_tx_macinabanners_mode_1.gif'
                ],
                [
                    'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_mode.I.2',
                    'random_all',
                    'EXT:macina_banners/Resources/Public/Images/selicon_tt_content_tx_macinabanners_mode_2.gif'
                ],
            ],
            'size' => 1,
            'maxitems' => 1,
        ]
    ],
];

unset($renderType);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'tx_macinabanners_placement,tx_macinabanners_mode');
