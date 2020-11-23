<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'CKEditor Rich Text Editor 5',
    'description' => 'Integration of CKEditor5 as Rich Text Editor.',
    'category' => 'be',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-11.9.9',
            'rte_ckeditor' => '10.0.0-11.9.9',
        ],
    ],
];
