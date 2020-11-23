<?php

declare(strict_types=1);

namespace GeorgRinger\RteCkeditor5\Xclass;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\RteCKEditor\Form\Element\RichTextElement;

class XclassedRichTextElement extends RichTextElement
{

    protected function getCkEditorRequireJsModuleCode(string $fieldId): string
    {
        $configuration = $this->prepareConfigurationForEditor();

//        $externalPlugins = '';
//        foreach ($this->getExtraPlugins() as $extraPluginName => $extraPluginConfig) {
//            $configName = $extraPluginConfig['configName'] ?? $extraPluginName;
//            if (!empty($extraPluginConfig['config']) && is_array($extraPluginConfig['config'])) {
//                if (empty($configuration[$configName])) {
//                    $configuration[$configName] = $extraPluginConfig['config'];
//                } elseif (is_array($configuration[$configName])) {
//                    $configuration[$configName] = array_replace_recursive($extraPluginConfig['config'], $configuration[$configName]);
//                }
//            }
//            $configuration['extraPlugins'] .= ',' . $extraPluginName;
//            if (isset($this->data['parameterArray']['fieldConf']['config']['placeholder'])) {
//                $configuration['editorplaceholder'] = (string)$this->data['parameterArray']['fieldConf']['config']['placeholder'];
//            }
//
//            $externalPlugins .= 'CKEDITOR.plugins.addExternal(';
//            $externalPlugins .= GeneralUtility::quoteJSvalue($extraPluginName) . ',';
//            $externalPlugins .= GeneralUtility::quoteJSvalue($extraPluginConfig['resource']) . ',';
//            $externalPlugins .= '\'\');';
//        }


        // @see https://ckeditor.com/docs/ckeditor5/latest/builds/guides/migrate.html#configuration-options-compatibility-table
        $newConfiguration = $editorResolved = [];
        if (isset($configuration['language'])) {
            $newConfiguration['language']['ui'] = $configuration['language'];
            unset($configuration['language']);
        }
        if (isset($configuration['contentsLanguage'])) {
            $newConfiguration['language']['content'] = $configuration['contentsLanguage'];
            unset($configuration['contentsLanguage']);
        }
        // no plugin loader!!
        if (isset($configuration['extraPlugins'])) {
//            $newConfiguration['extraPlugins'] = GeneralUtility::trimExplode(',', $configuration['extraPlugins'], true);
            unset($configuration['extraPlugins']);
        }
        if (isset($configuration['removePlugins'])) {
            $newConfiguration['removePlugins'] = GeneralUtility::trimExplode(',', $configuration['removePlugins'], true);
            unset($configuration['removePlugins']);
            $newConfiguration['removePlugins'][] = 'imageUpload';
            $newConfiguration['removePlugins'][] = 'mediaEmbed';
        }


        // editor then resolved
        if (isset($configuration['height']) && $configuration['height'] !== 'auto') {
            $editorResolved[] = 'editor.ui.view.editable.element.style.height = ' . GeneralUtility::quoteJSvalue($configuration['height'] . 'px') . ';';
        }
        if (isset($configuration['width']) && $configuration['width'] !== 'auto') {
            $editorResolved[] = 'editor.ui.view.editable.element.style.width = ' . GeneralUtility::quoteJSvalue($configuration['width'] . 'px') . ';';
        }

        // remove not needed
        foreach (['uiColor', 'entities_latin', 'entities', 'width', 'height', 'defaultLanguage', 'defaultContentLanguage', 'removeButtons'] as $notAvailable) {
            unset($configuration[$notAvailable]);
        }
        // needs extra plugin
        foreach (['extraAllowedContent'] as $extraPlugin) {
            unset($configuration[$extraPlugin]);
        }

        if ($_GET['debug'] == 1) {
            print_R($configuration);
            print_R($newConfiguration);
            die;
        }

        // links https://github.com/ckeditor/ckeditor5/issues/4836
        // external example https://github.com/basecondition/ckeditor5-rexlink/blob/master/src/rexlink.js

        // styling mit
        /*
         * .t3js-formengine-palette-field {
    --ck-color-text:red;
}
         */

        // nice
        // https://ckeditor5.github.io/docs/nightly/ckeditor5/latest/features/text-transformation.html
        // https://ckeditor5.github.io/docs/nightly/ckeditor5/latest/features/spelling-and-grammar-checking.html

        $jsonConfiguration = (string)json_encode($newConfiguration);

        // Make a hash of the configuration and append it to CKEDITOR.timestamp
        // This will mitigate browser caching issue when plugins are updated
        $configurationHash = GeneralUtility::shortMD5($jsonConfiguration);

        return 'function(CKEDITOR) {
                CKEDITOR.timestamp += "-' . $configurationHash . '";
                ' . $externalPlugins . '
                require([\'jquery\', \'TYPO3/CMS/Backend/FormEngine\'], function($, FormEngine) {
                    $(function(){
                        var escapedFieldSelector = \'#\' + $.escapeSelector(\'' . $fieldId . '\');

                        CKEDITOR.create( document.querySelector( "#' . $fieldId . '" ), ' . $jsonConfiguration . ')
            .then( editor => {
            console.log(editor.config);
                ' . implode(chr(10), $editorResolved) . '
        } )
    .catch( error => {
            console.error( error );
        } );

//                        CKEDITOR.replace("' . $fieldId . '", ' . $jsonConfiguration . ');
//                        CKEDITOR.instances["' . $fieldId . '"].on(\'change\', function(e) {
//                            var commands = e.sender.commands;
//                            CKEDITOR.instances["' . $fieldId . '"].updateElement();
//                            FormEngine.Validation.validate();
//                            FormEngine.Validation.markFieldAsChanged($(escapedFieldSelector));
//
//                            // remember changes done in maximized state and mark field as changed, once minimized again
//                            if (typeof commands.maximize !== \'undefined\' && commands.maximize.state === 1) {
//                                CKEDITOR.instances["' . $fieldId . '"].on(\'maximize\', function(e) {
//                                    $(this).off(\'maximize\');
//                                    FormEngine.Validation.markFieldAsChanged($(escapedFieldSelector));
//                                });
//                            }
//                        });

                        document.addEventListener(\'inline:sorting-changed\', function() {
//                            CKEDITOR.instances["' . $fieldId . '"].destroy();
//                            CKEDITOR.replace("' . $fieldId . '", ' . $jsonConfiguration . ');
                        });
                        $(document).on(\'flexform:sorting-changed\', function() {
//                            CKEDITOR.instances["' . $fieldId . '"].destroy();
//                            CKEDITOR.replace("' . $fieldId . '", ' . $jsonConfiguration . ');
                        });
                    });
                });
        }';


        return 'function(CKEDITOR) {
                 CKEDITOR.create( document.querySelector( "#' . $fieldId . '" ) )
            .then( editor => {
            console.log( editor );
        } )
    .catch( error => {
            console.error( error );
        } );
        }';
    }
}
