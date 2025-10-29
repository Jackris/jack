<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/crm.deal.bundle.css',
	'js' => 'dist/crm.deal.bundle.js',
	'rel' => [
		'main.core',
	],
	'skip_core' => false,
];
