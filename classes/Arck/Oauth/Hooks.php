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
}