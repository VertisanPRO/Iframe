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

const PAGE = 'Iframe';

$GLOBALS['IframeLanguage'] = $IframeLanguage;
$page_title = $IframeLanguage->get('general', 'title');

require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get Pages
$routePage = $_GET['route'];
if (preg_match('/\/$/', $routePage)) {
    $routePage = substr($routePage, 0, -1);
}
$page = DB::getInstance()->get('iframe_pages', ['url', '=', $routePage])->results();
$page = $page['0'];

// Get iframes content
$iframes = DB::getInstance()->get('iframe_data', ['page_id', '=', $page->id])->results();
$iframe_list = [];

if (count($iframes)) {
    foreach ($iframes as $iframe) {
        $iframe_list[] = [
            'name' => $iframe->name,
            'src' => $iframe->src,
            'size' => $iframe->iframe_size,
            'description' => Output::getDecoded($iframe->description),
            'footer_description' => Output::getDecoded($iframe->footer_description)
        ];
    }
    $smarty->assign([
        'IFRAME_LIST' => $iframe_list
    ]);
}

$template_file = 'Iframe/index.tpl';

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);
$template->onPageLoad();

$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

$template->displayTemplate($template_file, $smarty);