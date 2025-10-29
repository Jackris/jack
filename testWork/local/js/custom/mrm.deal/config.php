<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/mrm.deal.bundle.css',
	'js' => 'dist/mrm.deal.bundle.js',
	'rel' => [
		'main.core',
	],
	'skip_core' => false,
];
