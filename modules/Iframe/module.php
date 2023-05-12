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

class Iframe extends Module
{

    private $_language, $IframeLanguage;

    public function __construct($language, $pages)
    {
        $this->_language = $language;

        $this->IframeLanguage = $GLOBALS['IframeLanguage'];

        $name = 'Iframe';
        $author = '<a href="https://github.com/VertisanPRO" target="_blank" rel="nofollow noopener">VertisanPRO</a>';
        $module_version = '1.3.2';
        $nameless_version = '2.1.0';
        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        $pages->add('Iframe', '/panel/iframe', 'pages/panel/iframe.php');
        $pages->add('Iframe', '/panel/iframe/setting', 'pages/panel/setting.php');

        if (DB::getInstance()->showTables('iframe_pages') || DB::getInstance()->showTables('iframe_data')) {

            $iframes_pages = DB::getInstance()->get('iframe_pages', ['id', '<>', 0])->results();
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
    }

    public function onUninstall()
    {
    }

    public function onEnable()
    {
        try {
            DB::getInstance()->createTable("iframe_pages", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `url` varchar(255) NOT NULL, PRIMARY KEY (`id`)");
            DB::getInstance()->createTable("iframe_data", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `src` varchar(5000) NOT NULL, `iframe_size` varchar(255) NOT NULL, `page_id` int(11) NOT NULL, `description` text NULL, `footer_description` text NULL, PRIMARY KEY (`id`)");
            $group = DB::getInstance()->get('groups', ['id', '=', 2])->results();
            $group = $group[0];

            $group_permissions = json_decode($group->permissions, TRUE);
            $group_permissions['admincp.iframe'] = 1;

            $group_permissions = json_encode($group_permissions);
            DB::getInstance()->update('groups', 2, ['permissions' => $group_permissions]);
        } catch (Exception $e) {
            // Error
        }
    }

    public function onDisable()
    {
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template)
    {
        PermissionHandler::registerPermissions('Iframe', [
            'admincp.iframe' => $this->IframeLanguage->get('general', 'group_permission')
        ]);

        $icon = '<i class="nav-icon fas fa-crop-alt"></i>';
        $order = 44;

        if (defined('BACK_END')) {
            $title = $this->IframeLanguage->get('general', 'title');
            if ($user->hasPermission('admincp.iframe')) {
                $navs[2]->add('iframe_divider', mb_strtoupper($title, 'UTF-8'), 'divider', 'top', null, $order);
                $navs[2]->add('iframe_items', $title, URL::build('/panel/iframe'), 'top', null, $order + 0.1, $icon);
            }
        }
    }

    public function getDebugInfo(): array
    {
        return [];
    }
}