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

$IframeLanguage = $GLOBALS['IframeLanguage'];
$page_title = $IframeLanguage->get('general', 'title');

if ($user->isLoggedIn()) {
    if (!$user->canViewStaffCP()) {
        Redirect::to(URL::build('/'));
    }
    if (!$user->isAdmLoggedIn()) {
        Redirect::to(URL::build('/panel/auth'));
    } else {
        if (!$user->hasPermission('admincp.iframe')) {
            require_once(ROOT_PATH . '/403.php');
        }
    }
} else {
    // Not logged in
    Redirect::to(URL::build('/login'));
}

const PAGE = 'panel';
const PARENT_PAGE = 'iframe_items';
const PANEL_PAGE = 'iframe_items';

require_once(ROOT_PATH . '/core/templates/backend_init.php');

$iframes_pages = DB::getInstance()->get('iframe_pages', ['id', '<>', 0])->results();
$pages_list = [];
if (count($iframes_pages)) {
    foreach ($iframes_pages as $page) {
        $pages_list[] = [
            'edit_link' => URL::build('/panel/iframe', 'action=edit&id=' . Output::getClean($page->id)),
            'delete_link' => URL::build('/panel/iframe', 'action=delete&id=' . Output::getClean($page->id)),
            'setting_link' => URL::build('/panel/iframe/setting', 'action=setting&id=' . Output::getClean($page->id)),
            'id' => $page->id,
            'name' => $page->name,
            'url' => $page->url
        ];
    }
}
;

$smarty->assign([
    'SUBMIT' => $language->get('general', 'submit'),
    'YES' => $language->get('general', 'yes'),
    'NO' => $language->get('general', 'no'),
    'BACK' => $language->get('general', 'back'),
    'BACK_LINK' => URL::build('/panel/iframe'),
    'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
    'CONFIRM_DELETE' => $language->get('general', 'confirm_delete'),
    'TITLE' => $IframeLanguage->get('general', 'title'),
    'NAME' => $language->get('admin', 'page_title'),
    'URL' => $language->get('admin', 'page_path'),
    'PAGES' => $language->get('admin', 'pages'),
    'ADD_PAGE' => $language->get('admin', 'new_page'),
    'NO_PAGES' => $language->get('admin', 'no_custom_pages'),
    'PAGES_LIST' => $pages_list
]);

$template_file = 'Iframe/iframe.tpl';

if (!isset($_GET['action'])) {
    if (Input::exists()) {
        $errors = [];
        try {
            if (Token::check(Input::get('token'))) {
                $validation = Validate::check($_POST, [
                    'url' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 32
                    ],
                    'name' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 32
                    ]
                ]);
                if (preg_match('/^\//', Input::get('url'))) {
                    if ($validation->passed()) {
                        try {
                            DB::getInstance()->insert('iframe_pages', [
                                'name' => Input::get('name'),
                                'url' => Input::get('url')
                            ]);
                            Session::flash('staff', $language->get('admin', 'page_created_successfully'));
                            Redirect::to(URL::build('/panel/iframe'));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        $errors[] = $IframeLanguage->get('general', 'add_errors');
                    }
                } else {
                    $errors[] = $IframeLanguage->get('general', 'required_matches_errors');
                }
            } else {
                $errors[] = $language->get('general', 'invalid_token');
            }
        } catch (Exception $e) {
            // Error
        }
    }
} else {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                try {
                    DB::getInstance()->delete('iframe_pages', ['id', '=', $_GET['id']]);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                Session::flash('staff', $language->get('admin', 'page_deleted_successfully'));
                Redirect::to(URL::build('/panel/iframe'));
            }
            break;
        case 'edit':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                Redirect::to(URL::build('/panel/iframe'));
            }
            $edit_page = DB::getInstance()->get('iframe_pages', ['id', '=', $_GET['id']])->results();
            if (!count($edit_page)) {
                Redirect::to(URL::build('/panel/iframe'));
            }
            $edit_page = $edit_page[0];
            if (Input::exists()) {
                $errors = [];
                try {
                    if (Token::check(Input::get('token'))) {
                        $validation = Validate::check($_POST, [
                            'url' => [
                                'required' => true,
                                'min' => 2,
                                'max' => 32
                            ],
                            'name' => [
                                'required' => true,
                                'min' => 2,
                                'max' => 32
                            ]
                        ]);
                        if (preg_match('/^\//', Input::get('url'))) {
                            if ($validation->passed()) {
                                try {
                                    DB::getInstance()->update('iframe_pages', $edit_page->id, [
                                        'name' => Input::get('name'),
                                        'url' => Input::get('url')
                                    ]);
                                    Session::flash('staff', $language->get('admin', 'page_updated_successfully'));
                                    Redirect::to(URL::build('/panel/iframe'));
                                } catch (Exception $e) {
                                    $errors[] = $e->getMessage();
                                }
                            } else {
                                $errors[] = $IframeLanguage->get('general', 'edit_errors');
                            }
                        } else {
                            $errors[] = $IframeLanguage->get('general', 'required_matches_errors');
                        }
                    } else {
                        $errors[] = $language->get('general', 'invalid_token');
                    }
                } catch (Exception $e) {
                    // Error
                }
            }
            $smarty->assign([
                'EDIT_NAME' => Output::getClean($edit_page->name),
                'EDIT_URL' => Output::getClean($edit_page->url)
            ]);
            $template_file = 'Iframe/edit_page.tpl';
            break;
        default:
            Redirect::to(URL::build('/panel/iframe'));
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);
$template->onPageLoad();

if (Session::exists('staff'))
    $success = Session::flash('staff');

if (isset($success))
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$smarty->assign([
    'TOKEN' => Token::get(),
]);

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);