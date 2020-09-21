# Recaptcha

### Set up

Set site key and private key in `.env`
```
GOOGLE_RECAPTCHA_TICKBOX_SITE_KEY=
GOOGLE_RECAPTCHA_TICKBOX_SECRET=
GOOGLE_RECAPTCHA_ANDROID_SITE_KEY=
GOOGLE_RECAPTCHA_ANDROID_SECRET=
GOOGLE_RECAPTCHA_ENABLED=
```

### Get site key

`/v1/recaptcha`

```
   "data": {
        "id": "1",
        "type": "recaptcha",
        "attributes": {
            "reCAPTCHA-v2-android": {
                "type": "reCAPTCHA-v2-android",
                "siteKey": "{{android sitekey}}"
            },
            "reCAPTCHA-v2-tickbox": {
                "type": "reCAPTCHA-v2-tickbox",
                "siteKey": "{{tickbox sitekey}}"
            }
        }
    }
```

### Use Recaptcha

In response body
```
    "meta": {
    	"captcha": {
    		"type": "reCAPTCHA-v2-tickbox",
    		"response": "{{response}}"
    	}
    }
```

### Routes that need Recaptcha

* `/v1/users`