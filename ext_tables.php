<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_macinabanners_banners');

/* Set up the tt_content fields for the frontend plugins */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['macina_banners_pi1'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['macina_banners_pi1'] = 'tx_macinabanners_placement;;;;1-1-1, tx_macinabanners_mode';

// initalize 'context sensitive help' (csh)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_macinabanners_banners',
    'EXT:macina_banners/Resources/Private/Language/locallang_csh_banners.xlf');

#\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('macina_banners', 'pi1/static/', 'Bannermodule');
