<?php

namespace Arck\Oauth;

$server = Server::init();
$response = Server::response();
$request = Server::getRequest();

if (!$server->validateAuthorizeRequest($request, $response)) {
    $error = json_decode($response->getResponseBody());

    echo elgg_view_title('Error', ['class' => 'title']);
    
    echo elgg_view('output/longtext', [
        'value' => $error->error_description
    ]);
    return;
}

$application = Client::getDetailsFromID($vars['id']);

if (!$application) {
    echo elgg_echo('oauth:error:client:invalid:id');
    return;
}

echo elgg_view_title($application['name'], ['class' => 'has-text-centered title']);

echo elgg_view('output/longtext', [
    'value' => $application['description'],
    'class' => 'has-text-centered'
]);

if (elgg_is_logged_in()) {
    // has previously authorized?
    // @TODO - a more formal relationship between the user and the application, that could potentially be revoked
    // for basic use case we'll assume it's ok if they have an existing access token
    // if there are more scopes than our user scope, we will need a more robust check
    $existing_tokens = elgg_get_entities_from_metadata([
        'type' => 'object',
        'subtype' => AccessToken::SUBTYPE,
        'owner_guid' => elgg_get_logged_in_user_guid(),
        'metadata_name_value_pairs' => [
            [
                'name' => 'client_id',
                'value' => get_input('client_id')
            ]
        ],
        'count' => true
    ]);

    $params = [
        'client_id' => get_input('client_id'),
        'scope' => get_input('scope'),
        'user' => elgg_get_logged_in_user_entity()
    ];

    $has_authorized = elgg_trigger_plugin_hook('oauth', 'has_authorized', $params, $existing_tokens > 0);

    if (get_input('submitted') || $has_authorized) {
        $res = $server->handleAuthorizeRequest($request, $response, true, elgg_get_logged_in_user_guid());

        $res->send();

        exit;
    }

    echo elgg_view('output/longtext', [
        'value' => elgg_echo('oauth:authorize:prompt', [$application['name']]),
        'style' => 'margin: 0 auto; max-width: 600px;'
    ]);

    echo '<div class="has-text-centered">';
    echo elgg_view('output/url', [
        'text' => elgg_echo('oauth:authorize'),
        'href' => elgg_http_add_url_query_elements(current_page_url(), ['submitted' => 1]),
        'class' => 'elgg-button elgg-button-action',
        'style' => 'margin-top: 30px;'
    ]);
    echo '</div>';
}
else {
    echo elgg_view('output/longtext', [
        'value' => elgg_echo('oauth:login:authorize', [elgg_get_site_entity()->name]),
        'class' => 'has-text-centered'
    ]);

    echo elgg_view_form('login', [
        'action' => 'action/oauth/login',
        'style' => 'margin: 0 auto; width: 50%; min-width: 20rem; max-width: 485px'
    ]);
}