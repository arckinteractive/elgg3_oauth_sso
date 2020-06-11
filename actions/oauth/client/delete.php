<?php

namespace Arck\Oauth;

$entity = get_entity(get_input('guid'));

if (!$entity instanceof Client) {
    register_error(elgg_echo('oauth:error:client:invalid:guid'));
    forward(REFERER);
}

$entity->delete();

system_message('oauth:success:client:delete');

forward(REFERER);