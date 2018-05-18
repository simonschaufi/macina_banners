<?php

namespace JBartels\MacinaBanners\UserFunction;

use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TCA
{
    /**
     * @param array $parameters
     * @param \TYPO3\CMS\Backend\Form\Element\UserElement $pObj
     *
     * @return string
     */
    public function donateField(array $parameters, UserElement $pObj)
    {
        $headline = LocalizationUtility::translate('tx_macinabanners_donate.headline', 'macina_banners');
        $text = LocalizationUtility::translate('tx_macinabanners_donate.text', 'macina_banners');
        $donateButtonText = LocalizationUtility::translate('tx_macinabanners_donate.donate_button_text', 'macina_banners');
        return '<h4>' . $headline .'</h4>
            <p>' . $text . ' <i class="fa fa-smile-o" aria-hidden="true"></i></p>
            <a href="https://www.paypal.me/simonschaufi" target="_blank" class="btn btn-default"><i class="fa fa-paypal" aria-hidden="true"></i> ' . $donateButtonText . '</a>
        ';
    }
}
