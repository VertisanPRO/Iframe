<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.0.0-pr7
 *
 *  License: MIT
 *
 *  Iframe By xGIGABAITx
 */

class Iframe extends Module
{

	private $_language, $IframeLanguage;

	public function __construct($language, $pages, $INFO_MODULE)
	{
		$this->_language = $language;

		$this->IframeLanguage = $GLOBALS['IframeLanguage'];

		$name = $INFO_MODULE['name'];
		$author = $INFO_MODULE['author'];
		$module_version = $INFO_MODULE['module_ver'];
		$nameless_version = $INFO_MODULE['nml_ver'];
		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		$pages->add('Iframe', '/panel/iframe', 'pages/panel/iframe.php');
		$pages->add('Iframe', '/panel/iframe/setting', 'pages/panel/setting.php');

		$queries = new Queries();

		if ($queries->tableExists('iframe_pages') || $queries->tableExists('iframe_data')) {

			$iframes_pages = $queries->getWhere('iframe_pages', array('id', '<>', 0));
			if (count($iframes_pages)) {
				foreach ($iframes_pages as $page) {
					$url = trim($page->url);
					$pages->add('Iframe', $url, 'pages/index.php', 'Iframe', true);
				}
			}
		}
	}

	public function onInstall()
	{
		// Queries

		$queries = new Queries();

		try {

			$data = $queries->createTable("iframe_pages", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `url` varchar(255) NOT NULL, PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$data = $queries->createTable("iframe_data", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `src` varchar(5000) NOT NULL, `iframe_size` varchar(255) NOT NULL, `page_id` int(11) NOT NULL, `description` text NOT NULL, `footer_description` text NOT NULL, PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=utf8");
		} catch (Exception $e) {
			// Error
		}
	}

	public function onUninstall()
	{
	}

	public function onEnable()
	{

		$queries = new Queries();

		try {

			$group = $queries->getWhere('groups', array('id', '=', 2));
			$group = $group[0];

			$group_permissions = json_decode($group->permissions, TRUE);
			$group_permissions['admincp.iframe'] = 1;

			$group_permissions = json_encode($group_permissions);
			$queries->update('groups', 2, array('permissions' => $group_permissions));
		} catch (Exception $e) {
			// Ошибка
		}
	}

	public function onDisable()
	{
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template)
	{

		PermissionHandler::registerPermissions('Iframe', array(
			'admincp.iframe' => $this->IframeLanguage->get('general', 'group_permision')
		));


		$icon = '<i class="nav-icon fas fa-crop-alt"></i>';
		$order = 44;

		if (defined('BACK_END')) {

			$title =  $this->IframeLanguage->get('general', 'title');


			if ($user->hasPermission('admincp.iframe')) {

				$navs[2]->add('iframe_divider', mb_strtoupper($title, 'UTF-8'), 'divider', 'top', null, $order, '');

				$navs[2]->add('iframe_items', $title, URL::build('/panel/iframe'), 'top', null, $order + 0.1, $icon);
			}
		}
	}
}
