<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2003 Wolfgang Becker (wb@macina.com)
 *  (c) 2017 Jan Bartels
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace JBartels\MacinaBanners\Plugin;

/**
 * Plugin 'Bannermodule' for the 'macina_banners' extension.
 *
 * @author Wolfgang Becker <wb@macina.com>
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

/**
 * Class for creating a banner extension
 *
 * @author    Wolfgang <wb@macina.com>
 * @package TYPO3
 * @subpackage tx_macinabanners
 */
class Pi1 extends AbstractPlugin
{
    public $prefixId = 'tx_macinabanners_pi1';
    public $extKey = 'macina_banners';

    /**
     * @var string
     */
    protected $siteRelPath;

    /**
     * @var string
     */
    protected $imageName;

    /**
     * @var string
     */
    protected $altText;

    /**
     * @var string
     */
    protected $templateCode;

    /**
     * main function invoked by index.php
     *
     * @param    string $content : main variable carriing the content
     * @param    array $conf : config array from typoscript
     *
     * @return    string        html content
     */
    function main($content, $conf)
    {
        $this->pi_USER_INT_obj = 1; // Any link to yourself won't expect to be cached (no cHash and no_cache=1)

        // click forwarder
        if ($this->piVars['banneruid']) {
            $this->conf = $conf;
            $this->pi_setPiVarDefaults();
            $this->pi_loadLL('EXT:macina_banners/Resources/Private/Language/locallang.xlf');

            // get link details
            $record = $this->pi_getRecord('tx_macinabanners_banners', $this->piVars['banneruid'], $checkPage = 0);

            if ($record != false) {
                // update clicks
                $this->getDatabaseConnection()->exec_UPDATEquery('tx_macinabanners_banners',
                    'uid=' . $this->getDatabaseConnection()->fullQuoteStr($record['uid'], 'tx_macinabanners_banners'),
                    ['clicks' => ++$record['clicks']]);

                unset($this->piVars['banneruid']);
                HttpUtility::redirect($this->cObj->getTypoLink_URL($record['url']));
            }
        }

        switch ((string)$conf['CMD']) {
            case 'singleView':
                list($t) = explode(':', $this->cObj->currentRecord);
                $this->internal['currentTable'] = $t;
                $this->internal['currentRow'] = $this->cObj->data;
                return $this->singleView($content, $conf);
                break;
            default:
                if (strstr($this->cObj->currentRecord, 'tt_content')) {
                    $conf['pidList'] = $this->cObj->data['pages'];
                    $conf['recursive'] = $this->cObj->data['recursive'];
                    $conf['placement'] = $this->cObj->data['tx_macinabanners_placement'];
                    $conf['mode'] = $this->cObj->data['tx_macinabanners_mode'];
                }
                /* medialights: didn't work with deactivated caching in 'ext_localconf.php -> addPItoST43'*/
                //PRS+ 12.08.2005
                if ($conf['mode'] == 'random' || $conf['mode'] == 'random_all' || $conf['enableParameterRestriction'] == 1) {
                    $substKey = 'INT_SCRIPT.' . $GLOBALS['TSFE']->uniqueHash();
                    $link = '<!--' . $substKey . '-->';
                    $conf['userFunc'] = Pi1::class . '->listView';
                    $GLOBALS['TSFE']->config['INTincScript'][$substKey] = [
                        'conf' => $conf,
                        'cObj' => serialize($this->cObj),
                        'type' => 'FUNC',
                    ];

                    return $link;
                } else {
                    return $this->listView($content, $conf);
                }
                //PRS- 12.08.2005
        }
    }

    /**
     * main function for output of the listview and the singleview
     *
     * @param string $content : main variable carriing the content
     * @param array $conf : config array from typoscript
     *
     * @return string html content
     */
    function listView($content, array $conf)
    {
        $this->conf = $conf; // Setting the TypoScript passed to this function in $this->conf

        $this->pi_setPiVarDefaults();
        $this->pi_loadLL('EXT:macina_banners/Resources/Private/Language/locallang.xlf');

        $this->siteRelPath = $GLOBALS['TYPO3_LOADED_EXT'][$this->extKey]['siteRelPath'];

        // passende sprache oder sprachunabhaengig
        $where = '(sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_uid . ' OR sys_language_uid = -1)';

        $where .= $this->cObj->enableFields('tx_macinabanners_banners');

        // only banners with the correct placement (left right top bottom)
        $allowedPlacements = $conf['placement'] ? GeneralUtility::trimExplode(',', $conf['placement']) : [];
        if (count($allowedPlacements) > 0) {
            $placementClauses = [];
            /*
                FIX: Patch for custom categories
                inspiration: https://www.typo3.net/forum/thematik/zeige/thema/82762/?show=1
            */
            foreach ($allowedPlacements as $key => $placement) {
                if (GeneralUtility::inList('top,bottom,right,left', $placement)) {
                    $allowedPlacements[$key] = $placement;
                } else {
                    $catWhere = 'description LIKE \'%' . $placement . '%\'';
                    $catRS = $this->getDatabaseConnection()->exec_SELECTquery('*', 'tx_macinabanners_categories', $catWhere);
                    $catRow = $this->getDatabaseConnection()->sql_fetch_assoc($catRS);
                    $allowedPlacements[$key] = 'tx_macinabanners_categories:' . $catRow['uid'];
                }
                $placementClauses[] = $this->getDatabaseConnection()->listQuery('placement', $allowedPlacements[$key], 'tx_macinabanners_banners');
            }
            $where .= ' AND ' . implode(' OR ', $placementClauses);
        }

        // alle banner die die aktuelle page id nicht in excludepages stehen haben
        $where .= " AND NOT ( excludepages regexp '[[:<:]]" . $GLOBALS['TSFE']->id . "[[:>:]]' )";

        // pidList beachten
        if (!empty($conf['pidList'])) {
            if (!empty($conf['pidList.'])) {
                $where .= ' AND pid IN (' . $this->cObj->cObjGetSingle($conf['pidList'], $conf['pidList.']) . ') ';
            } else {
                $where .= ' AND pid IN (' . $this->getDatabaseConnection()->cleanIntList($conf['pidList']) . ') ';
            }
        }

        $orderBy = 'sorting';

        //medialights
        $queryPerformed = true;
        //Prepare and execute listing query
        if (isset($conf['enableParameterRestriction']) && !empty($conf['enableParameterRestriction'])) {
            //show only banners that match to the selected options
            $parameters = [];

            //get banners list according to parameters
            $RS = $this->getDatabaseConnection()->exec_SELECTquery('uid, parameters', 'tx_macinabanners_banners', $where, '', $orderBy);
            while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($RS)) {
                if (!empty($row['parameters'])) {
                    $lines = GeneralUtility::trimExplode(chr(10), $row['parameters']);
                    foreach ($lines AS $line) {
                        list($parameterName, $details) = GeneralUtility::trimExplode('=', $line);
                        $values = GeneralUtility::trimExplode(',', $details);

                        foreach ($values AS $value) {
                            if (isset($parameters[$parameterName][$value])) {
                                $parameters[$parameterName][$value] .= ',' . $row['uid'];
                            } else {
                                $parameters[$parameterName][$value] = $row['uid'];
                            }
                        }
                    }
                }
            }

            $currentParameters = GeneralUtility::_GET() + GeneralUtility::_POST();

            $ids = '';
            foreach ($currentParameters as $parameter => $value) {
                if (!empty($value) && isset($parameters[$parameter][$value])) {
                    if ($ids != '') {
                        $ids .= ',';
                    }
                    $ids .= $parameters[$parameter][$value];
                }
            }

            if ($ids != '') {
                $res = $this->getDatabaseConnection()->exec_SELECTquery('*', 'tx_macinabanners_banners', 'uid IN (' . $ids . ')');
            } else {
                $queryPerformed = false;
            }
        } else {
            //show all banners
            $res = $this->getDatabaseConnection()->exec_SELECTquery('*', 'tx_macinabanners_banners', $where, '', $orderBy);
        }

        // for caching
        $parentArr = [];

        // banner aussortieren
        $bannerdata = [];
        while ($queryPerformed && $row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            if ($row['pages'] && $row['recursiv']) { // wenn pages nicht leer und rekursiv angehakt ist
                // generiert zur aktuellen Seite alle Elternseiten und prueft ob in der Schnittemenge der
                // Elternseiten mit der erlaubten Bannerseiten mindestens ein Eintrag drinnen ist.
                if (count($parentArr) == 0) {
                    foreach ($GLOBALS['TSFE']->tmpl->rootLine as $tmp) {
                        $parentArr[] = $tmp['uid'];
                    }
                }

                $bannerPidArr = array_unique(GeneralUtility::trimExplode(',', $row['pages'], 1));
                $diffArr = array_intersect($parentArr, $bannerPidArr);

                if (count($diffArr) > 0) {
                    $bannerdata[] = $row;
                }
            } elseif ($row['pages'] && !$row['recursiv']) {
                // wenn pages nicht leer und rekursiv nicht angehakt ist
                $pidArray = array_unique(GeneralUtility::trimExplode(',', $row['pages'], 1));
                if (in_array($GLOBALS['TSFE']->id, $pidArray)) {
                    $bannerdata[] = $row;
                }
            } else {
                // wenn pages leer und rekursiv nicht angehakt ist
                $bannerdata[] = $row;
            }
        }

        $count = count($bannerdata);
        // use mode "random"
        if ($conf['mode'] == 'random' && $count > 1) {
            $randomselectnum = rand(0, $count - 1);
            $randombanner = $bannerdata[$randomselectnum];
            unset($bannerdata);
            $bannerdata[] = $randombanner;
        } elseif ($conf['mode'] == 'random_all' && $count > 1) {
            //media lights: use mode "random_all"
            shuffle($bannerdata);
        }

        $templateFile = $this->conf['templateFile'];
        if (version_compare(TYPO3_version, '8.7.0', '>=')) {
            $template = GeneralUtility::getFileAbsFileName($templateFile);
            if ($template !== '' && file_exists($template)) {
                $this->templateCode = file_get_contents($template);
            }
        } else {
            // compatibility for TYPO3 version lower than 8.7
            $this->templateCode = $this->cObj->fileResource($templateFile);
        }

        $templateMarker = '###template_banners###';
        $rowmarker = '###row###';

        if (version_compare(TYPO3_version, '8.7.0', '>=')) {
            $template = $this->templateService->getSubpart($this->templateCode, $templateMarker);
            $tableRowArray = $this->templateService->getSubpart($template, $rowmarker);
        } else {
            // compatibility for TYPO3 version lower than 8.7
            $template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
            $tableRowArray = $this->cObj->getSubpart($template, $rowmarker);
        }

        $rowData = '';

        // limit number of banners shown
        $qt = $conf['results_at_a_time'] < count($bannerdata) ? $conf['results_at_a_time'] : count($bannerdata);

        for ($i = 0; $i < $qt; $i++) {
            $row = $bannerdata[$i];

            // update impressions on rendering banner
            $this->getDatabaseConnection()->exec_UPDATEquery('tx_macinabanners_banners',
                'uid=' . $this->getDatabaseConnection()->fullQuoteStr($row['uid'], 'tx_macinabanners_banners'),
                ['impressions' => ++$row['impressions']]);

            // assign borders to array
            $styles = [
                'margin-top' => $row['border_top'],
                'margin-right' => $row['border_right'],
                'margin-bottom' => $row['border_bottom'],
                'margin-left' => $row['border_left']
            ];

            $wrappedSubpartArray = [];
            switch ($row['bannertype']) {
                case 0:
                    /*
                     * Grafik per Typoscript nach belieben zu konfigurieren
                     * Danke an Gernot Ploiner
                     */
                    $img = $this->conf['image.'];
                    $img['file'] = 'uploads/tx_macinabanners/' . $row['image'];
                    $img['alttext'] = $row['alttext'];

                    $this->imageName = 'uploads/tx_macinabanners/' . $row['image'];
                    array_walk_recursive($img, [$this, 'replace_field_image']);

                    $this->altText = $row['alttext'];
                    array_walk_recursive($img, [$this, 'replace_field_alttext']);

                    $img = $this->cObj->cObjGetSingle('IMAGE', $img);

                    // link image with page link und banner uid as getvar
                    if ($row['url']) {
                        $linkArray = explode(' ', $row['url']);
                        $wrappedSubpartArray['###bannerlink###'] = GeneralUtility::trimExplode('|',
                            $this->cObj->getTypoLink('|', $GLOBALS['TSFE']->id . ' ' . $linkArray[1], [
                                'no_cache' => 1,
                                $this->prefixId . '[banneruid]' => $row['uid']
                            ]));
                        $banner = join($wrappedSubpartArray['###bannerlink###'], $img);
                    } else {
                        $banner = $img;
                    }
                    break;
                case 1:
                    if ($row['url']) {
                        $linkArray = explode(' ', $row['url']);
                        $clickTAG = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $this->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, [
                                'no_cache' => 1,
                                $this->prefixId . '[banneruid]' => $row['uid']
                            ]);
                    }

                    $banner = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"; width="' . $row['flash_width'] . '" height="' . $row['flash_height'] . '">';
                    /**
                     * Fixed Bug #9381 Working in IE, not working in FF/Safari/Opera
                     */
                    $banner .= '<param name="movie" value="uploads/tx_macinabanners/' . $row['swf'] . '?clickTAG=' . urlencode($clickTAG) . '&amp;target=' . $linkArray[1] . '" />';
                    $banner .= '<param name="quality" value="autohigh" />';
                    $banner .= '<param name="allowScriptAccess" value="sameDomain" />';
                    $banner .= '<param name="menu" value="false" />';
                    $banner .= '<param name="wmode" value="transparent" />';
                    $banner .= '<embed src="uploads/tx_macinabanners/' . $row['swf'] . '?clickTAG=' . urlencode($clickTAG) . '&amp;target=' . $linkArray[1] . '" quality="autohigh" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer"; type="application/x-shockwave-flash" width="' . $row['flash_width'] . '" height="' . $row['flash_height'] . '"></embed>';
                    $banner .= '</object>';
                    break;
                case 2:
                    $banner = $row['html'];
                    break;
            }

            // function to attach styles to wrapping cell
            $banner = $this->wrapWithStyles($banner, $styles);

            // create the content by replacing the marker in the template
            $markerArray = [
                '###banner###' => $banner,
                '###alttext###' => $row['alttext'],
                '###url###' => $row['url'],
                '###impressions###' => $row['impressions'],
                '###clicks###' => $row['clicks'],
                '###edit###' => $this->pi_getEditPanel($row, 'tx_macinabanners_banners'),
            ];

            if ($row['bannertype'] == 0) {
                $markerArray['###filename###'] = $row['image'];
            } elseif ($row['bannertype'] == 1) {
                $markerArray['###filename###'] = $row['swf'];
            } else {
                $markerArray['###filename###'] = '';
            }

            if (version_compare(TYPO3_version, '8.7.0', '>=')) {
                $rowData .= $this->templateService->substituteMarkerArrayCached($tableRowArray, $markerArray, [], $wrappedSubpartArray);
            } else {
                // compatibility for TYPO3 version lower than 8.7
                $rowData .= $this->cObj->substituteMarkerArrayCached($tableRowArray, $markerArray, [], $wrappedSubpartArray);
            }
        }
        if ($rowData) {
            $subpartArray = [];
            $subpartArray['###row###'] = $rowData;
            if (version_compare(TYPO3_version, '8.7.0', '>=')) {
                $content = $this->templateService->substituteMarkerArrayCached($template, [], $subpartArray, []);
            } else {
                // compatibility for TYPO3 version lower than 8.7
                $content = $this->cObj->substituteMarkerArrayCached($template, [], $subpartArray, []);
            }
            return $content;
        } else {
            return '';  // no banners
        }
    }

    /**
     * output of a single view element called by pi_list_makelist
     *
     * @param    string $content : main variable carriing the content
     * @param    array $conf : config array from typoscript
     *
     * @return    string        html content
     */
    function singleView($content, $conf)
    {
        $this->conf = $conf;

        $this->pi_setPiVarDefaults();
        $this->pi_loadLL('EXT:macina_banners/Resources/Private/Language/locallang.xlf');

        switch ($this->internal['currentRow']['bannertype']) {
            case 0:
                /*
                 * Grafik per Typoscript nach belieben zu konfigurieren
                 * Danke an Gernot Ploiner
                 */
                $img = $this->conf['image.'];
                $img['file'] = 'uploads/tx_macinabanners/' . $this->internal['currentRow']['image'];
                $img['alttext'] = $this->internal['currentRow']['alttext'];

                $this->imageName = 'uploads/tx_macinabanners/' . $this->internal['currentRow']['image'];
                array_walk_recursive($img, [$this, 'replace_field_image']);

                $this->altText = $this->internal['currentRow']['alttext'];
                array_walk_recursive($img, [$this, 'replace_field_alttext']);

                $img = $this->cObj->cObjGetSingle('IMAGE', $img);

                // link image with pagelink und banneruid as getvar
                if ($this->internal['currentRow']['url']) {
                    $linkArray = explode(' ', $this->internal['currentRow']['url']);
                    $wrappedSubpartArray['###bannerlink###'] = GeneralUtility::trimExplode('|',
                        $this->cObj->getTypoLink("|", $GLOBALS['TSFE']->id . ' ' . $linkArray[1], [
                            'no_cache' => 1,
                            $this->prefixId . '[banneruid]' => $this->internal['currentRow']['uid']
                        ]));
                    $banner = join($wrappedSubpartArray['###bannerlink###'], $img);
                } else {
                    $banner = $img;
                }
                $content = '<table border="0" cellspacing="0" cellpadding="0"><tr><td nowrap valign="top">' . $banner . '</td></tr></table>';
                break;

            case 1:
                if ($this->internal['currentRow']['url']) {
                    $linkArray = explode(' ', $this->internal['currentRow']['url']);
                    $clickTAG = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $this->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, [
                            'no_cache' => 1,
                            $this->prefixId . '[banneruid]' => $this->internal['currentRow']['uid']
                        ]);
                }
                $banner = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="' . $this->internal['currentRow']['flash_width'] . '" height="' . $this->internal['currentRow']['flash_height'] . '">';
                $banner .= '<param name="movie" value="uploads/tx_macinabanners/' . $this->internal['currentRow']['swf'] . '" />';
                $banner .= '<param name="quality" value="high" />';
                $banner .= '<param name="allowScriptAccess" value="sameDomain" />';
                $banner .= '<param name="menu" value="false" />';
                $banner .= '<param name="wmode" value="transparent" />';
                $banner .= '<param name="FlashVars" value="clickTAG=' . urlencode($clickTAG) . '&amp;target=' . $linkArray[1] . '" />';
                $banner .= '<embed src="uploads/tx_macinabanners/' . $this->internal['currentRow']['swf'] . '" FlashVars="clickTAG=' . urlencode($clickTAG) . '&amp;target=' . $linkArray[1] . '" quality="high" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' . $this->internal['currentRow']['flash_width'] . '" height="' . $this->internal['currentRow']['flash_height'] . '"></embed>';
                $banner .= '</object>';
                $content = $banner;
                break;

            case 2:
                $content .= $this->internal['currentRow']['html'];
                break;
        }

        return $content;
    }

    /**
     * wrapWithStyles wraps the banner with a table that creates the borders left right top and bottom
     *
     * @param string $string : contains the html banner code
     * @param array $styles : named array with the styles padding-top, padding bottom ...
     *
     * @return string html content
     */
    protected function wrapWithStyles($string, $styles)
    {
        $content = '<div style="';
        foreach ($styles as $key => $value) {
            $content .= $key . ':' . $value . 'px; ';
        }
        $content .= '">' . $string . '</div>';

        return $content;
    }

    /**
     * @param string $item
     * @param $key
     */
    protected function replace_field_image(&$item, $key)
    {
        if ($item == 'field_image') {
            $item = $this->imageName;
        }
    }

    /**
     * @param string $item
     * @param $key
     */
    protected function replace_field_alttext(&$item, $key)
    {
        if ($item == 'field_alttext') {
            $item = $this->altText;
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
