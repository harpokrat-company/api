# MFA

### Set up

`PATCH {{host}}:{{port}}/v1/users/{{currentUserId}}`

* Request Body :
```
{
    "data": {
    	"id": "{{currentUserId}}",
        "type": "users",
        "attributes": {
            "mfaActivated": true
        }
    }
}
```

### At JWT Creation

`POST {{host}}:{{port}}/v1/json-web-tokens`

* Response Body :
```
{
    "jsonapi": {
        "version": "1.0"
    },
    "data": {
        "type": "json-web-tokens",
        "id": "{{id}}",
        "attributes": {
            "token": "..."
        },
        "relationships": {
            "mfa": {
                "data": {
                    "type": "secure-actions",
                    "id": "{{mfa-secure-action-id}}"
                }
            }
        }
    }
}
```

#### Receive E-mail

Take code from e-mail `{{mfa-code}}`

#### Activate Secure Action

`PATCH {{host}}:{{port}}/v1/secure-actions/{{mfa-secure-action-id}}`

* Request Body
```
{
    "data": {
    	"id": "{{mfa-secure-action-id}}",
        "type": "secure-actions",
        "attributes": {
            "validated": true
        }
    },
    "meta": {
    	"token": "{{mfa-code}}"
    }
}
```
