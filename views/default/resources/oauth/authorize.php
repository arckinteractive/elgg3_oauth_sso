<?php

$url = current_page_url();

$title = elgg_echo('oauth:authorize:title');

$application = elgg_view('oauth/application', [
    'id' => get_input('client_id')
]);

$layout = elgg_view_layout('content', [
    'title' => false,
    'content' => $application,
    'sidebar' => false
]);

echo elgg_view_page($title, $layout);