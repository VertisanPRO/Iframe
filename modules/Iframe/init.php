<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.0.0-pr7
 *
 *  License: MIT
 *
 *  Iframe By VertisanPRO
 */

$INFO_MODULE = [
    'name' => 'Iframe',
    'author' => '<a href="https://github.com/GIGABAIT-Official" target="_blank" rel="nofollow noopener">VertisanPRO</a>',
    'module_ver' => '1.3.1',
    'nml_ver' => '2.0.0-pr13',
];

$IframeLanguage = new Language(ROOT_PATH . '/modules/' . $INFO_MODULE['name'] . '/language', LANGUAGE);

$GLOBALS['IframeLanguage'] = $IframeLanguage;

require_once(ROOT_PATH . '/modules/' . $INFO_MODULE['name'] . '/module.php');

$module = new Iframe($language, $pages, $INFO_MODULE);
