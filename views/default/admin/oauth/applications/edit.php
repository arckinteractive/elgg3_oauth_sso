<?php

namespace Arck\Oauth;

$application = get_entity(get_input('guid'));

$application = $application instanceof Client ? $application : null;

echo elgg_view_form('oauth/client/edit', [], [
    'entity' => $application
]);