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
        // if (!$['server']->verifyResourceResponse($p['request'], $p['response'], 'user')) {
        //     $r = $p['response'];
        //     $r->setError(401, 'invalid_scope', 'The access token does not have the required scope');
        //     return $r;
        // }

        $r = $p['response'];
        $r->setParameters([
            'test' => 1,
            'other' => 2
        ]);
        
        return $r;
    }
}