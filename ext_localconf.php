<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\RteCKEditor\Form\Element\RichTextElement::class] = [
    'className' => \GeorgRinger\RteCkeditor5\Xclass\XclassedRichTextElement::class,
];

foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'] as $key => $class) {
    if ($class === \TYPO3\CMS\RteCKEditor\Hook\PageRendererRenderPreProcess::class . '->addRequireJsConfiguration') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][$key]
         = \GeorgRinger\RteCkeditor5\Hook\PageRendererRenderPreProcess::class . '->addRequireJsConfiguration';
    }
}

