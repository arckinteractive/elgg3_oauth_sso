<?php

$entity = elgg_extract('entity', $vars);

$values = elgg_get_sticky_values('oauth/client/edit');
elgg_clear_sticky_form('oauth/client/edit');

$get_value = function($name) use ($entity, $values) {
    if (isset($values[$name])) {
        return $values[$name];
    }

    if ($entity->{$name}) {
        return $entity->{$name};
    }

    return null;
};

echo elgg_view_field([
    '#type' => 'text',
    '#label' => elgg_echo('oauth:client:name'),
    'name' => 'title',
    'value' => $get_value('title')
]);

echo elgg_view_field([
    '#type' => 'plaintext',
    '#label' => elgg_echo('oauth:client:description'),
    'name' => 'description',
    'value' => $get_value('description')
]);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => elgg_echo('oauth:client:redirect_uri'),
    'name' => 'redirect_uri',
    'value' => $get_value('redirect_uri')
]);

if ($entity && $entity->guid) {
    echo elgg_view_field([
        '#type' => 'hidden',
        'name' => 'guid',
        'value' => $entity->guid
    ]);

    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:client_id'),
        'name' => 'client_id',
        'value' => $entity->client_id,
        'disabled' => true
    ]);

    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:client_secret'),
        'name' => 'client_secret',
        'value' => $entity->client_secret,
        'disabled' => true
    ]);

    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:pub_key'),
        'value' => $entity->public_key,
        'disabled' => true
    ]);

    echo elgg_view_field([
        '#type' => 'text',
        '#label' => elgg_echo('oauth:client:priv_key'),
        'value' => $entity->private_key,
        'disabled' => true
    ]);

    echo elgg_view_field([
        '#type' => 'checkbox',
        'name' => 'regenerate_keys',
        'value' => 1,
        '#label' => elgg_echo('oauth:client:regenerate_keys')
    ]);
}

echo elgg_view_field([
    '#type' => 'submit',
    'value' => elgg_echo('submit')
]);