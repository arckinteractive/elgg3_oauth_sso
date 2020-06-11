## Oauth Server

Allows other sites to use the elgg server as an oauth identity manager

1. Register a new application at `/admin/applications`
1. Authorize the user by having them log in at `[url]/oauth/authorize?client_id=xxxxxxxx&state=xxxxxxxx&response_type=code&scope=user` where client_id is the generated id of the application, and the state is a random string to prevent CSRF attacks
1. The user will log in if necessary and authorize the application
1. The user will be redirected back to the redirect_uri with the original state in a query param and a code: `[redirect_uri]?state=xxxxxx&code=xxxxxxxx`
1. Make a POST request to `/oauth/token` with body params of

```
    {
        client_id: xxxxxxxx,
        client_secret: xxxxxxx,
        grant_type: 'authorization_code',
        redirect_uri: 'https://xxxxxxxxxxxxx',
        code: xxxxxxxxxx
    }
```

6. The result will be an access token

```
    {
        "access_token": "369e27dae447d3856fc538a217536b186cea1bc3",
        "expires_in": 3600,
        "token_type": "Bearer",
        "scope": "user",
        "refresh_token": "3c706473a576815c503a119626d674331becc4c8"
    }
```

