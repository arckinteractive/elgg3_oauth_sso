<?php

namespace Arck\Oauth;

use OAuth2\OpenID\Storage\UserClaimsInterface;
use OAuth2\Storage\AuthorizationCodeInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\Storage\RefreshTokenInterface;
use OAuth2\Storage\JwtBearerInterface;
use OAuth2\Storage\ScopeInterface;
use OAuth2\Storage\PublicKeyInterface;
use OAuth2\OpenID\Storage\AuthorizationCodeInterface as OpenIDAuthorizationCodeInterface;
use InvalidArgumentException;

class Storage implements AuthorizationCodeInterface,
    AccessTokenInterface,
    ClientCredentialsInterface,
    UserCredentialsInterface,
    RefreshTokenInterface,
    ScopeInterface,
    PublicKeyInterface,
    UserClaimsInterface,
    OpenIDAuthorizationCodeInterface
{

    public function __construct()
    {

    }

    /**
     * @param string $code
     * @return bool|mixed
     */
    public function getAuthorizationCode($code)
    {
        // return $this->getValue($this->config['code_key'] . $code);
        $ia = elgg_set_ignore_access(true);

        $codes = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => AuthCode::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'authorization_code',
                'value' => $code
            ],
            'limit' => 1
        ]);

        if (!$codes) {
            elgg_set_ignore_access($ia);
            return false;
        }

        $code = array_shift($codes);

        $array = [
            'authorization_code' => $code->authorization_code,
            'client_id' => $code->client_id,
            'user_id' => $code->owner_guid,
            'redirect_uri' => $code->redirect_uri,
            'expires' => $code->expires,
            'scope' => $code->scope,
            'id_token' => $code->id_token
        ];

        elgg_set_ignore_access($ia);

        return $array;
    }

    /**
     * @param string $authorization_code
     * @param mixed  $client_id
     * @param mixed  $user_id
     * @param string $redirect_uri
     * @param int    $expires
     * @param string $scope
     * @param string $id_token
     * @return bool
     */
    public function setAuthorizationCode($authorization_code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        $ia = elgg_set_ignore_access(true);

        $code = new AuthCode();
        $code->owner_guid = $user_id;
        $code->container_guid = $user_id;
        $code->client_id = $client_id;
        $code->redirect_uri = $redirect_uri;
        $code->expires = $expires + 600; //$expires;
        $code->scope = $scope;
        $code->id_token = $id_token;
        $code->authorization_code = $authorization_code;

        $return = $code->save();

        elgg_set_ignore_access($ia);

        error_log('set authorization code: ' . $authorization_code);
        error_log($return);

        return $return ? true : false;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function expireAuthorizationCode($code)
    {
        $code = elgg_get_entities_from_metadata([
            'type' => 'object',
            'subtype' => AuthCode::SUBTYPE,
            'metadata_name_value_pairs' => [
                'name' => 'authorization_code',
                'value' => $code
            ]
        ]);

        $return = false;

        if ($code) {
            foreach ($code as $c) {
                $return = $c->delete();
            }
        }

        return $return;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function checkUserCredentials($username, $password)
    {
        return elgg_authenticate($username, $password) === true;
    }

    /**
     * plaintext passwords are bad!  Override this for your application
     *
     * @param array  $user
     * @param string $password
     * @return bool
     */
    protected function checkPassword($user, $password)
    {
        return $this->checkUserCredentials($user['username'], $password);
    }

    // use a secure hashing algorithm when storing passwords. Override this for your application
    protected function hashPassword($password)
    {
        return sha1($password);
    }

    /**
     * @param string $username
     * @return array|bool|false
     */
    public function getUserDetails($username)
    {
        return $this->getUser($username);
    }

    /**
     * @param string $username
     * @return array|bool
     */
    public function getUser($username)
    {
        $user = get_user_by_username($username);

        // the default behavior is to use "username" as the user_id
        $details = [
            'user_id' => $username,
            'email' => $user->email,
            'name' => $user->getDisplayName(),
            'email_verified' => true,
        ];

        $details = elgg_trigger_plugin_hook_handler('oauth', 'user:details', ['user' => $user], $details);

        return $details;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $first_name
     * @param string $last_name
     * @return bool
     */
    public function setUser($username, $password, $first_name = null, $last_name = null)
    {
        // $password = $this->hashPassword($password);

        // return $this->setValue(
        //     $this->config['user_key'] . $username,
        //     compact('username', 'password', 'first_name', 'last_name')
        // );

        // shouldn't be used in Elgg...
        return false;
    }

    /**
     * @param mixed  $client_id
     * @param string $client_secret
     * @return bool
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $details = Client::getDetailsFromID($client_id);

        return isset($details['client_secret'])
            && $details['client_secret'] == $client_secret;
    }

    /**
     * @param $client_id
     * @return bool
     */
    public function isPublicClient($client_id)
    {
        if (!$client = $this->getClientDetails($client_id)) {
            return false;
        }

        return empty($client['client_secret']);
    }

    /**
     * @param $client_id
     * @return array|bool|mixed
     */
    public function getClientDetails($client_id)
    {
        return Client::getDetailsFromID($client_id);
    }

    /**
     * @param $client_id
     * @param null $client_secret
     * @param null $redirect_uri
     * @param null $grant_types
     * @param null $scope
     * @param null $user_id
     * @return bool
     */
    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {

        return Client::setDetails([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_types' => $grant_types,
            'scope' => $scope,
            'user_id' => $user_id
        ]);
    }

    /**
     * @param $client_id
     * @param $grant_type
     * @return bool
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, (array) $grant_types);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /**
     * @param $refresh_token
     * @return bool|mixed
     */
    public function getRefreshToken($refresh_token)
    {
        return RefreshToken::getDetailsFromToken($refresh_token);
    }

    /**
     * @param $refresh_token
     * @param $client_id
     * @param $user_id
     * @param $expires
     * @param null $scope
     * @return bool
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        return RefreshToken::setDetails([
            'refresh_token' => $refresh_token,
            'client_id' => $client_id,
            'user_id' => $user_id,
            'expires' => $expires,
            'scope' => $scope
        ]);
    }

    /**
     * @param $refresh_token
     * @return bool
     */
    public function unsetRefreshToken($refresh_token)
    {
        return RefreshToken::expire($refresh_token);
    }

    /**
     * @param string $access_token
     * @return array|bool|mixed|null
     */
    public function getAccessToken($access_token)
    {
        return AccessToken::getDetailsFromToken($access_token);
    }

    /**
     * @param string $access_token
     * @param mixed $client_id
     * @param mixed $user_id
     * @param int $expires
     * @param null $scope
     * @return bool
     */
    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        return AccessToken::setDetails([
            'access_token' => $access_token,
            'client_id' => $client_id,
            'user_id' => $user_id,
            'expires' => $expires,
            'scope' => $scope
        ]);
    }

    /**
     * @param $access_token
     * @return bool
     */
    public function unsetAccessToken($access_token)
    {
        return AccessToken::expire($access_token);
    }

    /**
     * @param $scope
     * @return bool
     */
    public function scopeExists($scope)
    {
        $scopes = [];

        // allow other plugins to add their own scopes
        $supportedScope = elgg_trigger_plugin_hook('oauth', 'scopes:available', $scopes, $scopes);
        $scope = explode(' ', $scope);

        return (count(array_diff($scope, $supportedScope)) == 0);
    }

    /**
     * @param null $client_id
     * @return bool|mixed
     */
    public function getDefaultScope($client_id = null)
    {
        if (!is_null($client_id)) {
            $clientDetails = Client::getDetailsFromID($client_id);
            if ($clientDetails && isset($clientDetails['scope']) && $clientDetails['scope'])  {
                return $clientDetails['scope'];
            }
        }

        $result = ['user'];

        // allow other plugins to modify the 
        $result = elgg_trigger_plugin_hook('oauth', 'scope:default', $result, $result);

        return $result;
    }

    /**
     * @param $client_id
     * @return bool|null
     */
    public function getClientScope($client_id)
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }

        return null;
    }

    /**
     * @param string $client_id
     * @return mixed
     */
    public function getPublicKey($client_id = '')
    {

        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        return $clientDetails['public_key'];
    }

    /**
     * @param string $client_id
     * @return mixed
     */
    public function getPrivateKey($client_id = '')
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        return $clientDetails['private_key'];
    }

    /**
     * @param null $client_id
     * @return mixed|string
     */
    public function getEncryptionAlgorithm($client_id = null)
    {
        $clientDetails = $this->getClientDetails($client_id);
        if ($clientDetails && $clientDetails['encryption_algorithm']) {
            return $clientDetails['encryption_algorithm'];
        }

        return 'RS256';
    }

    /**
     * @param mixed $user_id
     * @param string $claims
     * @return array|bool
     */
    public function getUserClaims($user_id, $claims)
    {
        $userDetails = $this->getUserDetails($user_id);
        if (!is_array($userDetails)) {
            return false;
        }

        $claims = explode(' ', trim($claims));
        $userClaims = array();

        // for each requested claim, if the user has the claim, set it in the response
        $validClaims = explode(' ', self::VALID_CLAIMS);
        foreach ($validClaims as $validClaim) {
            if (in_array($validClaim, $claims)) {
                if ($validClaim == 'address') {
                    // address is an object with subfields
                    $userClaims['address'] = $this->getUserClaim($validClaim, $userDetails['address'] ?: $userDetails);
                } else {
                    $userClaims = array_merge($userClaims, $this->getUserClaim($validClaim, $userDetails));
                }
            }
        }

        return $userClaims;
    }

    /**
     * @param $claim
     * @param $userDetails
     * @return array
     */
    protected function getUserClaim($claim, $userDetails)
    {
        $userClaims = array();
        $claimValuesString = constant(sprintf('self::%s_CLAIM_VALUES', strtoupper($claim)));
        $claimValues = explode(' ', $claimValuesString);

        foreach ($claimValues as $value) {
            if ($value == 'email_verified') {
                $userClaims[$value] = true;
            } else {
                $userClaims[$value] = isset($userDetails[$value]) ? $userDetails[$value] : null;
            }
        }

        return $userClaims;
    }
}