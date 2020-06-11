<?php

namespace Arck\Oauth;

class RefreshToken extends \ElggObject {
    const SUBTYPE = 'oauth_refresh_token';

    /**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
     * return array
     */
    public static function getDetailsFromToken($token) {
        $ia = elgg_set_ignore_access(true);

        $refreshToken = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => self::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'refresh_token',
                'value' => $token
			],
			'limit' => 1
        ]);

        if ($refreshToken) {
			if (is_array($refreshToken)) {
				$refreshToken = $refreshToken[0];
            }

            $res = [
				'refresh_token' => $refreshToken->refresh_token,
				'client_id' => $refreshToken->client_id,
				'user_id' => $refreshToken->owner_guid,
				'expires' => $refreshToken->expires,
				'scope' => $refreshToken->scope
            ];
            
            elgg_set_ignore_access($ia);

            return $res;
        }

        elgg_set_ignore_access($ia);

        return [];
    }

    /**
     * return bool
     */
    public static function setDetails($params) {
        $site = elgg_get_site_entity();

        $ia = elgg_set_ignore_access(true);

        $token = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => self::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'refresh_token',
                'value' => $params['refresh_token']
            ]
        ]);

        if ($token && is_array($token)) {
            $token = $token[0];
        }
        else {
            $token = new RefreshToken();
        }

        foreach ($params as $key => $v) {
            $token->{$key} = $v;
        }

        $token->owner_guid = $params['user_id'] ? : $site->guid;
        $token->container_guid = $params['user_id'] ? : $site->guid;

        $return = $token->save();

        elgg_set_ignore_access($ia);

        return $return;
    }

    public static function expire($refresh_token) {
        $ia = elgg_set_ignore_access(true);

        $token = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => self::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'refresh_token',
                'value' => $refresh_token
            ]
        ]);

        $return = false;

        foreach ($token as $t) {
            $return = $t->delete();
        }

        elgg_set_ignore_access($ia);

        return $return;
    }
}