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
require(ROOT_PATH . '/core/includes/markdown/tohtml/Markdown.inc.php'); // Markdown to HTML

$iframes = $queries->getWhere('iframe_data', array('page_id', '=', $_GET['id']));
if (count($iframes)) {
	$iframes_list = array();
	foreach ($iframes as $iframe) {
		$iframes_list[] = array(
			'edit_link' => URL::build('/panel/iframe/setting', 'action=frame_edit&name=' . Output::getClean($iframe->id)),
			'delete_link' => URL::build('/panel/iframe/setting', 'action=delete&name=' . Output::getClean($iframe->id)),
			'id' => $iframe->id,
			'name' => $iframe->name,
			'src' => $iframe->src,
			'iframe_size' => $iframe->iframe_size,
			'page_id' => $iframe->page_id,
		);
	}
	$smarty->assign(array(
		'IFRAME_LIST' => $iframes_list
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
	'SRC' => $IframeLanguage->get('general', 'src'),
	'IFRAME' => $IframeLanguage->get('general', 'iframe'),
	'ADD_IFRAME' => $IframeLanguage->get('general', 'add_iframe'),
	'NO_IFRAME' => $IframeLanguage->get('general', 'no_iframe'),
	'IFRAME_SIZE' => $IframeLanguage->get('general', 'iframe_size'),
	'DESCRIPTION' => $language->get('admin', 'page_content'),
	'FOOTER_DESCRIPTION' => $IframeLanguage->get('general', 'footer_description')
));


$template_file = 'Iframe/setting.tpl';

if (isset($_POST['add'])) {

	if (Input::exists()) {
		$errors = array();
		if (Token::check(Input::get('token'))) {

			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'src' => array(
					'required' => true,
					'min' => 2,
				),
				'name' => array(
					'required' => true,
					'min' => 2,
					'max' => 32
				)
			));

			// Parse markdown
			$cache->setCache('content_formatting');
			$formatting = $cache->retrieve('formatting');

			if ($formatting == 'markdown') {
				$content = Michelf\Markdown::defaultTransform(Input::get('content'));
				$content = Output::getClean($content);

				$footer_content = Michelf\Markdown::defaultTransform(Input::get('footer_content'));
				$footer_content = Output::getClean($footer_content);
			} else {
				$content = Output::getClean(Input::get('content'));
				$footer_content = Output::getClean(Input::get('footer_content'));
			}

			if ($validation->passed()) {
				try {

					$queries->create('iframe_data', array(
						'name' => Input::get('name'),
						'src' => Input::get('src'),
						'iframe_size' => Input::get('iframe_size'),
						'page_id' => $_GET['id'],
						'description' => $content,
						'footer_description' => $footer_content
					));

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
	}
} else {
	switch ($_GET['action']) {

		case 'delete':
			if (isset($_GET['name']) && is_numeric($_GET['name'])) {
				try {

					$page_id = $queries->getWhere('iframe_data', array('id', '=', $_GET['name']));
					$page_id = $page_id['0'];
					$page_id = $page_id->page_id;

					$queries->delete('iframe_data', array('id', '=', $_GET['name']));
				} catch (Exception $e) {
					die($e->getMessage());
				}

				Session::flash('staff', $language->get('admin', 'page_deleted_successfully'));
				Redirect::to(URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id));
				die();
			}
			break;

		case 'frame_edit':

			if (!isset($_GET['name']) || !is_numeric($_GET['name'])) {
				Redirect::to(URL::build('/panel/iframe'));
				die();
			}
			$edit_iframe = $queries->getWhere('iframe_data', array('id', '=', $_GET['name']));
			$edit_iframe = $edit_iframe[0];
			$page_id = $edit_iframe->page_id;

			if (Input::exists()) {
				$errors = array();
				if (Token::check(Input::get('token'))) {

					$validate = new Validate();
					$validation = $validate->check($_POST, array(
						'src' => array(
							'required' => true,
							'min' => 2
						),
						'name' => array(
							'required' => true,
							'min' => 2,
							'max' => 32
						)
					));

					// Parse markdown
					$cache->setCache('content_formatting');
					$formatting = $cache->retrieve('formatting');

					if ($formatting == 'markdown') {
						$content = Michelf\Markdown::defaultTransform(Input::get('content'));
						$content = Output::getClean($content);

						$footer_content = Michelf\Markdown::defaultTransform(Input::get('footer_content'));
						$footer_content = Output::getClean($footer_content);
					} else {
						$content = Output::getClean(Input::get('content'));
						$footer_content = Output::getClean(Input::get('footer_content'));
					}


					if ($validation->passed()) {
						try {

							$queries->update('iframe_data', $edit_iframe->id, array(
								'name' => Input::get('name'),
								'src' => Input::get('src'),
								'iframe_size' => Input::get('iframe_size'),
								'description' => $content,
								'footer_description' => $footer_content
							));


							Session::flash('staff', $language->get('admin', 'page_updated_successfully'));
							Redirect::to(URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id));
							die();
						} catch (Exception $e) {
							$errors[] = $e->getMessage();
						}
					} else {
						$errors[] = $IframeLanguage->get('general', 'edit_errors');
					}
				} else {
					$errors[] = $language->get('general', 'invalid_token');
				}
			}

			$smarty->assign(array(
				'EDIT_NAME' => Output::getClean($edit_iframe->name),
				'EDIT_SRC' => Output::getClean($edit_iframe->src),
				'SIZE' => (int) $edit_iframe->iframe_size,
				'CONTENT' => $edit_iframe->description,
				'FOOTER_CONTENT' => $edit_iframe->footer_description,
				'BACK_LINK' => URL::build('/panel/iframe/setting', 'action=edit&id=' . $page_id)
			));


			$template_file = 'Iframe/edit_iframe.tpl';

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




// Get post formatting type (HTML or Markdown)
$cache->setCache('content_formatting');
$formatting = $cache->retrieve('formatting');

if ($formatting == 'markdown') {
	// Markdown
	$smarty->assign('MARKDOWN', true);
	$smarty->assign('MARKDOWN_HELP', $language->get('general', 'markdown_help'));

	$template->addJSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/emoji/js/emojione.min.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/emojionearea/js/emojionearea.min.js' => array()
	));

	$template->addJSScript('
		$(document).ready(function() {
			var el = $("#markdown").emojioneArea({
				pickerPosition: "bottom"
				});
				});
        ');
} else {
	$template->addJSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/prism/prism.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/plugins/spoiler/js/spoiler.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/tinymce.min.js' => array()
	));

	$template->addJSScript(Input::createTinyEditor($language, 'reply'));
	$template->addJSScript(Input::createTinyEditor($language, 'footer_reply'));
}






require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);
