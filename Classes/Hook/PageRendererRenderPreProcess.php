<?php

declare(strict_types=1);



namespace GeorgRinger\RteCkeditor5\Hook;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

final class PageRendererRenderPreProcess
{
    public function addRequireJsConfiguration(array $params, PageRenderer $pageRenderer): void
    {
        if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_BE) {
            $pageRenderer->addRequireJsConfiguration([
                'shim' => [
                    'ckeditor' => ['exports' => 'CKEDITOR']
                ],
                'paths' => [
                    'ckeditor' => PathUtility::getAbsoluteWebPath(
                        ExtensionManagementUtility::extPath('rte_ckeditor5', 'Resources/Public/JavaScript/Contrib/')
                    ) . 'ckeditor'
                ]
            ]);
        }
    }
}
