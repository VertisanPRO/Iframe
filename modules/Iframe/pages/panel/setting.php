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

$iframes = DB::getInstance()->get('iframe_data', ['page_id', '=', $_GET['id']])->results();
$iframes_list = [];
if (count($iframes)) {
    foreach ($iframes as $iframe) {
        $iframes_list[] = [
            'edit_link' => URL::build('/panel/iframe/setting', 'action=frame_edit&name=' . Output::getClean($iframe->id)),
            'delete_link' => URL::build('/panel/iframe/setting', 'action=delete&name=' . Output::getClean($iframe->id)),
            'id' => $iframe->id,
            'name' => $iframe->name,
            'src' => $iframe->src,
            'iframe_size' => $iframe->iframe_size,
            'page_id' => $iframe->page_id,
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
    'SRC' => $IframeLanguage->get('general', 'src'),
    'IFRAME' => $IframeLanguage->get('general', 'iframe'),
    'ADD_IFRAME' => $IframeLanguage->get('general', 'add_iframe'),
    'NO_IFRAME' => $IframeLanguage->get('general', 'no_iframe'),
    'IFRAME_SIZE' => $IframeLanguage->get('general', 'iframe_size'),
    'DESCRIPTION' => $language->get('admin', 'page_content'),
    'FOOTER_DESCRIPTION' => $IframeLanguage->get('general', 'footer_description'),
    'IFRAME_LIST' => $iframes_list
]);

$template_file = 'Iframe/setting.tpl';
if (isset($_POST['add'])) {
    if (Input::exists()) {
        $errors = [];
        try {
            if (Token::check(Input::get('token'))) {
                $validation = Validate::check($_POST, [
                    'src' => [
                        'required' => true,
                        'min' => 2,
                    ],
                    'name' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 32
                    ]
                ]);
                $content = Output::getClean(Input::get('content'));
                $footer_content = Output::getClean(Input::get('footer_content'));
                if ($validation->passed()) {
                    try {
                        DB::getInstance()->insert('iframe_data', [
                            'name' => Input::get('name'),
                            'src' => Input::get('src'),
                            'iframe_size' => Input::get('iframe_size'),
                            'page_id' => $_GET['id'],
                            'description' => $content,
                            'footer_description' => $footer_content
                        ]);
                        Session::flash('staff', $language->get('admin', 'page_created_successfully'));
                        Redirect::to(URL::build('/panel/iframe/setting', 'action=edit&id=' . $_GET['id']));
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                } else {
                    $errors[] = $IframeLanguage->get('general', 'add_errors');
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
            if (isset($_GET['name']) && is_numeric($_GET['name'])) {
                try {
                    $page_id = DB::getInstance()->get('iframe_data', ['id', '=', $_GET['name']])->results();
                    $page_id = $page_id['0'];
                    $page_id = $page_id->page_id;
                    DB::getInstance()->delete('iframe_data', ['id', '=', $_GET['name']]);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                Session::flash('staff', $language->get('admin', 'page_deleted_successfully'));
                Redirect::to(URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id));
            }
            break;
        case 'frame_edit':
            if (!isset($_GET['name']) || !is_numeric($_GET['name'])) {
                Redirect::to(URL::build('/panel/iframe'));
            }
            $edit_iframe = DB::getInstance()->get('iframe_data', ['id', '=', $_GET['name']])->results();
            $edit_iframe = $edit_iframe[0];
            $page_id = $edit_iframe->page_id;
            if (Input::exists()) {
                $errors = [];
                try {
                    if (Token::check(Input::get('token'))) {
                        $validation = Validate::check($_POST, [
                            'src' => [
                                'required' => true,
                                'min' => 2
                            ],
                            'name' => [
                                'required' => true,
                                'min' => 2,
                                'max' => 32
                            ]
                        ]);
                        $content = Output::getClean(Input::get('content'));
                        $footer_content = Output::getClean(Input::get('footer_content'));
                        if ($validation->passed()) {
                            try {
                                DB::getInstance()->update('iframe_data', $edit_iframe->id, [
                                    'name' => Input::get('name'),
                                    'src' => Input::get('src'),
                                    'iframe_size' => Input::get('iframe_size'),
                                    'description' => $content,
                                    'footer_description' => $footer_content
                                ]);
                                Session::flash('staff', $language->get('admin', 'page_updated_successfully'));
                                Redirect::to(URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id));
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        } else {
                            $errors[] = $IframeLanguage->get('general', 'edit_errors');
                        }
                    } else {
                        $errors[] = $language->get('general', 'invalid_token');
                    }
                } catch (Exception $e) {
                    // Error
                }
            }
            $smarty->assign([
                'EDIT_NAME' => Output::getClean($edit_iframe->name),
                'EDIT_SRC' => Output::getClean($edit_iframe->src),
                'SIZE' => (int) $edit_iframe->iframe_size,
                'CONTENT' => $edit_iframe->description,
                'FOOTER_CONTENT' => $edit_iframe->footer_description,
                'BACK_LINK' => URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id)
            ]);
            $template_file = 'Iframe/edit_iframe.tpl';
            break;
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

$template->assets()->include([
    AssetTree::TINYMCE,
]);
$template->addJSScript(Input::createTinyEditor($language, 'reply'));
$template->addJSScript(Input::createTinyEditor($language, 'footer_reply'));

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);