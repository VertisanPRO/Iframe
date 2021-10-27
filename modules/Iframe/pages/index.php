<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.0.0-pr7
 *
 *  License: MIT
 *
 *  Iframe By xGIGABAITx
 */

define('PAGE', 'Iframe');

$GLOBALS['IframeLanguage'] = $IframeLanguage;

// $page_title = $IframeLanguage->get('general', 'title');

require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get Pages
$routePage = $_GET['route'];
if (preg_match('/\/$/', $routePage)) {
	$routePage = substr($routePage, 0, -1);
}
$page = $queries->getWhere('iframe_pages', array('url', '=', $routePage));
$page = $page['0'];

// Get iframes content
$iframes = $queries->getWhere('iframe_data', array('page_id', '=', $page->id));
$iframe_list = array();

if (count($iframes)) {
	foreach ($iframes as $iframe) {
		$iframe_list[] = array(
			'name' => $iframe->name,
			'src' => $iframe->src,
			'size' => $iframe->iframe_size,
			'description' => Output::getDecoded($iframe->description),
			'footer_description' => Output::getDecoded($iframe->footer_description)
		);
	}

	$smarty->assign(array(
		'IFRAME_LIST' => $iframe_list
	));
}

$template_file = 'Iframe/index.tpl';



// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets, $template);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));



require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');



$template->displayTemplate($template_file, $smarty);
