# PasswordManagerBundle
Password management bundle for Symfony

## Installation
```json
// composer.json
{
    "require": {
        "cyve/password-manager-bundle": "^1.0"
    },
    "repositories": [
        {
            "type": "git",
            "url":  "git@bitbucket.org:cyve/password-manager-bundle.git"
        }
    ]
}
```
Create SSH key on prod machine and upload it on Bitbucket

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
