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
    if (get_input('submitted')) {
        $res = $server->handleAuthorizeRequest($request, $response, true);

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