<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin([
    'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
    'macina_banners_pi1'
], 'list_type', 'macina_banners');

$macinaCfg = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['macina_banners']);
$renderType = 'selectMultipleSideBySide';
if ($macinaCfg['renderMode'] == 'singlebox' || $macinaCfg['renderMode'] == 'checkbox') {
    $renderType = $macinaCfg['renderMode'];
}
unset ($macinaCfg);

$fields = [
    'tx_macinabanners_donate' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:macina_banners/Resources/Private/Language/locallang_db.xlf:tt_content.tx_macinabanners_donate',
        'config' => [
            'type' => 'user',
            'userFunc' => \JBartels\MacinaBanners\UserFunction\TCA::class . '->donateField',
            'parameters' => []
        ]
    ],
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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $fields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'tx_macinabanners_donate,tx_macinabanners_placement,tx_macinabanners_mode',
    '',
    'after:list_type'
);
