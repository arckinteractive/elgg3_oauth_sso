<?php

namespace Arck\Oauth;

use Elgg\Includer;
use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
    
    /**
	 * Get plugin root
	 * @return string
	 */
	protected function getRoot() {
		return $this->plugin->getPath();
	}

    /**
	 * Executed during 'plugin_boot:before', 'system' event
	 *
	 * Allows the plugin to require additional files, as well as configure services prior to booting the plugin
	 *
	 * @return void
	 */
	public function load() {
		Includer::requireFileOnce($this->getRoot() . '/autoloader.php');
	}

    public function init() {    
        elgg_register_page_handler('oauth', [PageHandlers::class, 'OauthPage']);
        elgg_register_plugin_hook_handler('public_pages', 'walled_garden', __NAMESPACE__ . '\\Hooks::publicPages');
        elgg_register_plugin_hook_handler('oauth', 'scopes:available', __NAMESPACE__ . '\\Hooks::scopesAvailable');
    
        elgg_register_admin_menu_item('administer', 'applications', 'oauth');
    
        // register api endpoints
        elgg_register_plugin_hook_handler('oauth', 'api:GET', __NAMESPACE__ . '\\Hooks::apiMe');
    }
}