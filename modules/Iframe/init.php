<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 *
 *  Iframe By VertisanPRO
 */

$IframeLanguage = new Language(ROOT_PATH . '/modules/' . 'Iframe' . '/language', LANGUAGE);
$GLOBALS['IframeLanguage'] = $IframeLanguage;

require_once(ROOT_PATH . '/modules/' . 'Iframe' . '/module.php');

$module = new Iframe($language, $pages);