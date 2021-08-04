<?php

namespace Arck\Oauth;

class Hooks {
    public static function publicPages($h, $t, $r, $p) {
        $r[] = 'oauth/authorize';
        $r[] = 'oauth/token';
        $r[] = 'oauth/api/.*';

        return $r;
    }

    public static function scopesAvailable($h, $t, $r, $p) {
        $r[] = 'user';

        return $r;
    }

    /**
     * handle oauth/api/me
     */
    public static function apiMe($h, $t, $r, $p) {
        if ($p['segments'][0] !== 'me' || count($p['segments']) > 1) {
            return $r;
        }

        // ensure user scope
        if (!$p['server']->verifyResourceRequest($p['request'], $p['response'], 'user')) {
            $r = $p['response'];
            $r->setError(401, 'invalid_scope', 'The access token does not have the required scope');
            return $r;
        }

        $exported_user = [
            'guid' => $p['user']->guid,
            'name' => $p['user']->name,
            'username' => $p['user']->username,
            'email' => $p['user']->email,
        ];

        $exported_user = elgg_trigger_plugin_hook('export', 'oauth_user', $p['user'], $exported_user);

        $r = $p['response'];
        $r->setParameters($exported_user);
        
        return $r;
    }

    /**
     * Database cleanup
     */
    public static function dailyCron() {

        elgg_call(ELGG_IGNORE_ACCESS, function() {
            // delete expired access tokens
            $access_tokens = elgg_get_entities([
                'type' => 'object',
                'subtype' => 'oauth_access_token',
                'metadata_name_value_pairs' => [
                    [
                        'name' => 'expires',
                        'value' => time(),
                        'operand' => '<',
                    ]
                ],
                'limit' => false,
                'batch' => true,
                'batch_inc_offset' => false
            ]);

            foreach ($access_tokens as $token) {
                $token->delete();
            }


            // delete expired authorization codes
            $auth_codes = elgg_get_entities([
                'type' => 'object',
                'subtype' => 'oauth_authorization_code',
                'metadata_name_value_pairs' => [
                    [
                        'name' => 'expires',
                        'value' => time(),
                        'operand' => '<',
                    ]
                ],
                'limit' => false,
                'batch' => true,
                'batch_inc_offset' => false
            ]);

            foreach ($auth_codes as $code) {
                $code->delete();
            }

            // delete expired authorization codes
            $refresh_tokens = elgg_get_entities([
                'type' => 'object',
                'subtype' => 'oauth_refresh_token',
                'metadata_name_value_pairs' => [
                    [
                        'name' => 'expires',
                        'value' => time(),
                        'operand' => '<',
                    ]
                ],
                'limit' => false,
                'batch' => true,
                'batch_inc_offset' => false
            ]);

            foreach ($refresh_tokens as $token) {
                $token->delete();
            }
        });
    }
}