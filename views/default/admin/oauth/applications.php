<?php

namespace Arck\Oauth;

echo '<div style="text-align: right; margin-bottom: 16px;">';
echo elgg_view('output/url', [
    'text' => elgg_view_icon('plus'),
    'href' => 'admin/oauth/applications/edit',
    'class' => 'elgg-button elgg-button-action'
]);
echo '</div>';

echo elgg_list_entities([
    'type' => 'object',
    'subtype' => Client::SUBTYPE,
    'limit' => 10,
    'no_results' => elgg_echo('oauth:application:no_results')
]);