<?php

namespace Arck\Oauth;

elgg_make_sticky_form('oauth/client/edit');

$name = get_input('title');
$description = get_input('description');
$redirect_uri = get_input('redirect_uri');
$application = get_entity(get_input('guid'));

$application = $application instanceof Client ? $application : null;

if (!$name || !$redirect_uri) {
    register_error(elgg_echo('oauth:client:required:fields'));
    forward(REFERER);
}

if (!$application) {
    $application = new Client();
    $application->client_id = _elgg_services()->crypto->getRandomString(12, \ElggCrypto::CHARS_PASSWORD);
}

$application->access_id = ACCESS_PUBLIC;
$application->title = $name;
$application->description = $description;
$application->redirect_uri = $redirect_uri;

if (!$application->client_secret || get_input('regenerate_keys')) {
    $application->client_secret = _elgg_services()->crypto->getRandomString(32, \ElggCrypto::CHARS_PASSWORD);
}

$application->save();
 
elgg_clear_sticky_form('oauth/client/edit');

system_message('Application has been saved');
forward(REFERER);