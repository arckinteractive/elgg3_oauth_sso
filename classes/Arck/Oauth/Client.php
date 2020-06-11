<?php

namespace Arck\Oauth;

class Client extends \ElggObject {

    const SUBTYPE = 'oauth_client';

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
    public static function getDetailsFromID($id) {
        $client = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => self::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'client_id',
                'value' => $id
            ],
            'limit' => 1
        ]);

        if ($client) {
            if (is_array($client)) {
                $client = $client[0];
            }

            return [
                'name' => $client->title,
                'description' => $client->description,
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
                'grant_types' => $client->grant_types,
                'redirect_uri' => $client->redirect_uri,
                'user_id' => $client->owner_guid,
                'scope' => $client->scope,
                'public_key' => $client->public_key,
                'private_key' => $client->private_key,
                'encryption_algorithm' => $client->encryption_algorithm ? : 'RS256'
            ];
        }

        return [];
    }

    /**
     * return bool
     */
    public static function setDetails($params) {
        $site = elgg_get_site_entity();

        $ia = elgg_set_ignore_access(true);

        $client = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => self::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'client_id',
                'value' => $params['client_id']
            ]
        ]);

        if ($client) {
            $client = $client[0];
        }
        else {
            $client = new Client();
        }

        foreach ($params as $key => $v) {
            $client->{$key} = $v;
        }

        $client->owner_guid = $params['user_id'] ? : $site->guid;
        $client->container_guid = $params['user_id'] ? : $site->guid;

        $return = $client->save();

        elgg_set_ignore_access($ia);

        return $return;
    }
}