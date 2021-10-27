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

$IframeLanguage = $GLOBALS['IframeLanguage'];
$page_title = $IframeLanguage->get('general', 'title');

if ($user->isLoggedIn()) {
	if (!$user->canViewStaffCP()) {

		Redirect::to(URL::build('/'));
		die();
	}
	if (!$user->isAdmLoggedIn()) {

		Redirect::to(URL::build('/panel/auth'));
		die();
	} else {
		if (!$user->hasPermission('admincp.iframe')) {
			require_once(ROOT_PATH . '/403.php');
			die();
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'iframe_items');
define('PANEL_PAGE', 'iframe_items');

require_once(ROOT_PATH . '/core/templates/backend_init.php');


$iframes_pages = $queries->getWhere('iframe_pages', array('id', '<>', 0));
if (count($iframes_pages)) {
	$pages_list = array();
	foreach ($iframes_pages as $page) {
		$pages_list[] = array(
			'edit_link' => URL::build('/panel/iframe', 'action=edit&id=' . Output::getClean($page->id)),
			'delete_link' => URL::build('/panel/iframe', 'action=delete&id=' . Output::getClean($page->id)),
			'setting_link' => URL::build('/panel/iframe/setting', 'action=setting&id=' . Output::getClean($page->id)),
			'id' => $page->id,
			'name' => $page->name,
			'url' => $page->url
		);
	}
	$smarty->assign(array(
		'PAGES_LIST' => $pages_list
	));
}

$smarty->assign(array(
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
	'NO_PAGES' => $language->get('admin', 'no_custom_pages')
));


$template_file = 'Iframe/iframe.tpl';


if (!isset($_GET['action'])) {

	if (Input::exists()) {
		$errors = array();
		if (Token::check(Input::get('token'))) {

			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'url' => array(
					'required' => true,
					'min' => 2,
					'max' => 32
				),
				'name' => array(
					'required' => true,
					'min' => 2,
					'max' => 32
				)
			));

			// validation matches '/'
			if (preg_match('/^\//', Input::get('url'))) {

				if ($validation->passed()) {
					try {

						$queries->create('iframe_pages', array(
							'name' => Input::get('name'),
							'url' => Input::get('url')
						));

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
	}
} else {
	switch ($_GET['action']) {

		case 'delete':
			if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				try {

					$queries->delete('iframe_pages', array('id', '=', $_GET['id']));
				} catch (Exception $e) {
					die($e->getMessage());
				}

				Session::flash('staff', $language->get('admin', 'page_deleted_successfully'));
				Redirect::to(URL::build('/panel/iframe'));
				die();
			}
			break;

		case 'edit':

			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				Redirect::to(URL::build('/panel/iframe'));
				die();
			}
			$edit_page = $queries->getWhere('iframe_pages', array('id', '=', $_GET['id']));
			if (!count($edit_page)) {
				Redirect::to(URL::build('/panel/iframe'));
				die();
			}

			$edit_page = $edit_page[0];

			if (Input::exists()) {
				$errors = array();
				if (Token::check(Input::get('token'))) {

					$validate = new Validate();
					$validation = $validate->check($_POST, array(
						'url' => array(
							'required' => true,
							'min' => 2,
							'max' => 32
						),
						'name' => array(
							'required' => true,
							'min' => 2,
							'max' => 32
						)
					));

					// validation matches '/'
					if (preg_match('/^\//', Input::get('url'))) {

						if ($validation->passed()) {
							try {

								$queries->update('iframe_pages', $edit_page->id, array(
									'name' => Input::get('name'),
									'url' => Input::get('url')
								));


								Session::flash('staff', $language->get('admin', 'page_updated_successfully'));
								Redirect::to(URL::build('/panel/iframe'));
								die();
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
			}

			$smarty->assign(array(
				'EDIT_NAME' => Output::getClean($edit_page->name),
				'EDIT_URL' => Output::getClean($edit_page->url)
			));


			$template_file = 'Iframe/edit_page.tpl';

			break;

		default:
			Redirect::to(URL::build('/panel/iframe'));
			die();
			break;
	}
}





















// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets, $template);
$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));
$template->onPageLoad();

if (Session::exists('staff'))
	$success = Session::flash('staff');

if (isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if (isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

$smarty->assign(array(
	'TOKEN' => Token::get(),
));

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);
