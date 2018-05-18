<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "macina_banners".
 *
 * Auto generated 04-01-2013 19:31
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'Advanced Banner Management',
	'description' => 'Banner management tool with banner placement on frontend, banner rotation, scheduling and statistics.',
	'category' => 'plugin',
	'version' => '1.6.1',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'author' => 'Wolfgang Becker',
	'author_email' => 'wolfgang.becker@visionate.com',
	'author_company' => '',
	'constraints' => [
        'depends' => [
			'php' => '5.6.0-7.2.99',
			'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
