<?php

namespace Arck\Oauth;

class AuthCode extends \ElggObject {
    const SUBTYPE = 'oauth_authorization_code';

    /**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}
}