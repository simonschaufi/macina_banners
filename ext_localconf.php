<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.tx_macinabanners_banners=1');

//medialights: deactivated caching
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('macina_banners','Classes/Plugin/Pi1.php','_pi1','list_type',0);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('macina_banners', 'Classes/Plugin/Pi1.php', '_pi1', 'list_type', 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('macina_banners', 'setup',
    'plugin.tx_macinabanners_pi1.userFunc = JBartels\\MacinaBanners\\Plugin\\Pi1->main');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('macina_banners', 'setup', '
	tt_content.shortcut.20.0.conf.tx_macinabanners_banners = < plugin.tx_macinabanners_banners_pi1
	tt_content.shortcut.20.0.conf.tx_macinabanners_banners.CMD = singleView
', 43);

// add New CE-wizard elements
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:macina_banners/Configuration/PageTS/NewContentElementWizard.typoscript">');

// Support for banners in the Page module
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['tx_macinabanners_banners'][0] = [
    'fList' => 'customer;image;url;placement,bannertype',
    'icon' => true
];
