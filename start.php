<?php

namespace Arck\Oauth;

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    elgg_register_page_handler('oauth', __NAMESPACE__ . '\\oauth_pagehandler');
    elgg_register_plugin_hook_handler('public_pages', 'walled_garden', __NAMESPACE__ . '\\Hooks::publicPages');
    elgg_register_plugin_hook_handler('oauth', 'scopes:available', __NAMESPACE__ . '\\Hooks::scopesAvailable');

    elgg_register_admin_menu_item('administer', 'applications', 'oauth');

    elgg_register_action('oauth/client/edit', __DIR__ . '/actions/oauth/client/edit.php', 'admin');
    elgg_register_action('oauth/applications/delete', __DIR__ . '/actions/oauth/client/delete.php', 'admin');
    elgg_register_action('oauth/login', __DIR__ . '/actions/oauth/login.php', 'public');

    // register api endpoints
    elgg_register_plugin_hook_handler('oauth', 'api:GET', __NAMESPACE__ . '\\Hooks::apiMe');
}

function oauth_pagehandler($segments) {
    switch ($segments[0]) {
        case 'authorize':
            echo elgg_view_resource('oauth/authorize');
            return true;
        break;
        case 'token':
            $server = Server::init();
            $response = Server::response();
            $request = Server::getRequest();

            $res = $server->handleTokenRequest($request, $response);

            $res->send();

            exit;
        break;
        case 'api':
            $server = Server::init();
            $response = Server::response();
            $request = Server::getRequest();

            // echo '<pre>' . print_r($request, 1) . '</pre>'; exit;

            if (!$server->verifyResourceRequest($request, $response)) {
                $res = $server->getResponse();
                $res->setError(401, 'invalid_request', 'Invalid request');
                $res->send();
                exit;
            }
            array_shift($segments);

            $token_data = $server->getAccessTokenData($request, $response);
            $user = get_user($token_data['user_id']);

            $params = [
                'server' => $server,
                'response' =>  $response,
                'request' => $request,
                'segments' => $segments,
                'token_data' => $token_data,
                'user' => $user
            ];

            // handled calls should return a Response
            // if a specific scope is required it should be re-checked with
            // $server->verifyResourceRequest($request, $response, $scope)
            $apiresponse = elgg_trigger_plugin_hook('oauth', 'api:' . $_SERVER['REQUEST_METHOD'], $params, null);

            if ($apiresponse === null) {
                $response->setError(401, 'invalid_request', 'Invalid request');
                $response->send();
                exit;
            }

            $response->send();
            exit;
    }

    return false;
}