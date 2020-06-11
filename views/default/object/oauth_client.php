<?php

$entity = elgg_extract('entity', $vars);

echo elgg_format_element('h3', [], $entity->title);

echo elgg_view('output/longtext', [
    'value' => $entity->description
]);

if ($entity->canEdit()) {
    echo '<div>';
    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:client_id'),
        'value' => $entity->client_id,
        'readonly' => true
    ]);

    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:client_secret'),
        'value' => $entity->client_secret,
        'readonly' => true
    ]);
    echo '</div>';

    echo elgg_view('output/url', [
        'text' => elgg_echo('edit'),
        'href' => elgg_normalize_url('admin/oauth/applications/edit?guid=' . $entity->guid),
    ]);

    echo '&nbsp; | &nbsp;';

    echo elgg_view('output/url', [
        'text' => elgg_echo('delete'),
        'href' => elgg_normalize_url('action/oauth/applications/delete?guid=' . $entity->guid),
        'confirm' => true
    ]);
}