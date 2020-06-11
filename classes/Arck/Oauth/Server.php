<?php

namespace Arck\Oauth;

use OAuth2\Server as OAuth2Server;
use OAuth2\OpenID\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Request;
use OAuth2\Response;

class Server {
    public static function init() {
        static $server;

        if ($server) {
            return $server;
        }

        $storage = new Storage();

        // create array of supported grant types
        $grantTypes = array(
            'authorization_code' => new AuthorizationCode($storage),
            'user_credentials'   => new UserCredentials($storage),
            'refresh_token'      => new RefreshToken($storage, array(
                'always_issue_new_refresh_token' => true,
            )),
        );

        // instantiate the oauth server
        $server = new OAuth2Server($storage, array(
            'enforce_state' => true,
            'allow_implicit' => true,
            'use_openid_connect' => true,
            'issuer' => $_SERVER['HTTP_HOST'],
        ), $grantTypes);

        return $server;
    }

    public static function response() {
        return new Response();
    }

    public static function getRequest() {
        return Request::createFromGlobals();
    }
}