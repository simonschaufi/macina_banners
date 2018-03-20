<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_macinabanners_banners');

$macinaCfg = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['macina_banners']);
$renderType = 'selectMultipleSideBySide';
if ($macinaCfg['renderMode'] == 'singlebox' || $macinaCfg['renderMode'] == 'checkbox') {
    $renderType = $macinaCfg['renderMode'];
}
unset ($macinaCfg);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners',
        'label' => 'customer',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'type' => 'bannertype',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'thumbnail' => 'image',
        'versioning'=>1,
        'versioning_followPages'=>1,
        'transOrigPointerField'=>'l18n_parent',
        'transOrigDiffSourceField'=>'l18n_diffsource',
        'languageField'=>'sys_language_uid',
        'dividers2tabs'=>1,
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'iconfile' => 'EXT:macina_banners/Resources/Public/Images/icon_tx_macinabanners_banners.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,sys_language_uid,t3ver_label,l18n_parent,customer,bannertype,image,maxw,alttext,url,swf,flash_width,flash_height,html,placement,border_top,border_right,border_bottom,border_left,pages,recursiv,excludepages,impressions,clicks,parameters'
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.disable',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 8,
                'max' => 20,
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 8,
                'max' => 20,
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ]
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 7,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        -1
                    ],
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        -2
                    ],
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        '--div--'
                    ]
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'enableMultiSelectFilterTextfield' => true
            ]
        ],
        'customer' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.customer',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required',
            ]
        ],
        'bannertype' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.bannertype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.bannertype.I.0', '0'],
                    ['LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.bannertype.I.1', '1'],
                    ['LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.bannertype.I.2', '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        't3ver_label' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1, 'flags-multiple'],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ]
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 0,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_macinabanners_banners',
                'foreign_table_where' => 'AND tx_macinabanners_banners.pid=###CURRENT_PID### AND  tx_macinabanners_banners.sys_language_uid IN (-1,0)',
            ]
        ],
        'l18n_diffsource' => [
            'config'=> ['type'=>'passthrough']
        ],
        'image' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.image',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => 1000,
                'uploadfolder' => 'uploads/tx_macinabanners',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'maxw' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.maxw',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'eval' => 'int,nospace',
            ]
        ],
        'alttext' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.alttext',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ],
        'url' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.url',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'max' => 255,
                'checkbox' => '',
                'eval' => 'trim',
                'softref' => 'typolink',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        'icon' => 'actions-wizard-link',
                        'module' => [
                            'name' => 'wizard_link'
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                        'params' => [
                            'blindLinkOptions' => 'folder,mail',
                            'blindLinkFields' => 'class, target'
                        ],
                    ]
                ]
            ]
        ],
        'swf' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.swf',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => '',
                'disallowed' => 'php,php3',
                'max_size' => 1000,
                'uploadfolder' => 'uploads/tx_macinabanners',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'flash_width' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.flash_width',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'eval' => 'required,int,nospace',
            ]
        ],
        'flash_height' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.flash_height',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'eval' => 'required,int,nospace',
            ]
        ],
        //medialights: new type html
        'html' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.html',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
        'placement' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.placement',
            'config' => [
                'type' => 'select',
                'itemsProcFunc' => JBartels\MacinaBanners\BackendHelper\Placement::class . '->main',
                'renderType' => $renderType,
                'size' => 5,
                'maxitems' => 50,
            ]
        ],
        'border_top' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.border_top',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 4,
                'range' => ['lower' => 0, 'upper' => 1000],
                'eval' => 'int,nospace',
            ]
        ],
        'border_right' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.border_right',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 4,
                'range' => ['lower' => 0, 'upper' => 1000],
                'eval' => 'int,nospace',
            ]
        ],
        'border_bottom' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.border_bottom',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 4,
                'range' => ['lower'=>0, 'upper'=>1000],
                'eval' => 'int,nospace',
            ]
        ],
        'border_left' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.border_left',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 4,
                'range' => ['lower' => 0, 'upper' => 1000],
                'eval' => 'int,nospace',
            ]
        ],
        'pages' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.pages',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 100,
            ]
        ],
        'recursiv' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.recursiv',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'excludepages' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.excludepages',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 100,
            ]
        ],
        'impressions' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.impressions',
            'config' => [
                'type' => 'none',
            ]
        ],
        'clicks' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.clicks',
            'config' => [
                'type' => 'none',
            ]
        ],
        'parameters' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.parameters',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.general,customer,l18n_parent,bannertype,sys_language_uid,parameters,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.bannerimage,image;;3;;1-1-1, alttext,url,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.view, placement;;;;1-1-1,pages, recursiv, excludepages,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.visibility,hidden;;1;;1-1-1,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.statistics,impressions, clicks'
        ],
        '1' => [
            'showitem' => '--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.general,customer,l18n_parent,bannertype,sys_language_uid,parameters,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.flashfilm,swf;;;;1-1-1, url, flash_width, flash_height;;2,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.view, placement;;;;1-1-1,pages, recursiv, excludepages,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.visibility,hidden;;1;;1-1-1,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.statistics,impressions, clicks'
        ],
        '2' => [
            'showitem' => '--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.general,customer,l18n_parent,bannertype,sys_language_uid,parameters,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.html,html,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.view, placement;;;;1-1-1,pages, recursiv, excludepages,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.visibility,hidden;;1;;1-1-1,--div--;LLL:EXT:macina_banners/Resources/Private/Languages/locallang_db.xlf:tx_macinabanners_banners.statistics,impressions, clicks'
        ],
    ],
    // '1' => array('showitem' => 'bannertype, sys_language_uid, l18n_parent, swf;;;;1-1-1, flash_width, flash_height;;2, placement;;;;1-1-1, pages, recursiv, excludepages, customer;;;;1-1-1, impressions, hidden;;1;;1-1-1')
    // '0' => array('showitem' => 'bannertype, sys_language_uid, l18n_parent, image;;3;;1-1-1, alttext, url, placement;;;;1-1-1, pages, recursiv, excludepages, customer;;;;1-1-1,impressions, clicks, hidden;;1;;1-1-1'),;
    'palettes' => [
        '1' => ['showitem' => 'starttime, endtime, --linebreak--, fe_group'],
        '2' => ['showitem' => 'border_left, border_top, border_right, border_bottom'],
        '3' => ['showitem' => 'maxw, border_left, border_top, border_right, border_bottom'],
        '4' => ['showitem' => ''],
        '5' => ['showitem' => 'impressions']
    ]
];
