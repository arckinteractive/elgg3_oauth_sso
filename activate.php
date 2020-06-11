<?php

namespace Arck\Oauth;

if (get_subtype_id('object', AccessToken::SUBTYPE)) {
	update_subtype('object', AccessToken::SUBTYPE, AccessToken::class);
} else {
	add_subtype('object', AccessToken::SUBTYPE, AccessToken::class);
}

if (get_subtype_id('object', AuthCode::SUBTYPE)) {
	update_subtype('object', AuthCode::SUBTYPE, AuthCode::class);
} else {
	add_subtype('object', AuthCode::SUBTYPE, AuthCode::class);
}

if (get_subtype_id('object', Client::SUBTYPE)) {
	update_subtype('object', Client::SUBTYPE, Client::class);
} else {
	add_subtype('object', Client::SUBTYPE, Client::class);
}

if (get_subtype_id('object', RefreshToken::SUBTYPE)) {
	update_subtype('object', RefreshToken::SUBTYPE, RefreshToken::class);
} else {
	add_subtype('object', RefreshToken::SUBTYPE, RefreshToken::class);
}