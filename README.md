# PasswordManagerBundle
Password management and recovery bundle for Symfony using [login links](https://symfony.com/doc/current/security/login_link.html).

## Installation
```bash
$ composer require cyve/password-manager-bundle`
```

## Configuration
```php
// config/bundles.php
return [
    ...
    Cyve\PasswordManagerBundle\CyvePasswordManagerBundle::class => ['all' => true],
];
```
```yaml
// config/routes.yaml
cyve_password_manager:
    resource: "@CyvePasswordManagerBundle/Resources/config/routing.yaml"
```
```yaml
// config/packages/security.yaml
security:
    firewalls:
        main:
            login_link:
                check_route: app_login_check # or any login-link route
                signature_properties: ['userIdentifier'] # add other properties if you want
```

/!\ The login link contains a `_target_path` query parameter to redirect the user to the `/password/update` route after the login. If you change the name of the parameter in the security config, the Symfony default redirection rules will be applied.

## Usage

### Update a password
In a browser, go to `/password/update` (require full authentication) and use the form to set a new password.

In a terminal, execute `bin/console cyve:password:reset <username> <password>`

### Reset a password
In a browser, go to `/password/request-login-link` and enter a user identifier. If the user exists, a notification email containing a login link will be sent to the user's email address. The user will be automatically redirected to the `/passord/update` page after a successful login.
