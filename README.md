# README 

`[Work in Progress]`

_This is an experimental repository, which is not a part of any responsible disclosure program._

### Installation (Development)

Before running `composer install` run `php config/generate.php` to create a dummy config class.  

### Conventions 

The project uses [phpstan](http://phpstan.org) for static anylisys 
and [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) for coding conventions. 

Running: 
- `composer run-script lint-autofix`
- `composer run-script phpstan`

Run everything and tests `composer run-script ci`.


### Environmental variables 

`QUICKBIKE_NO_CONFIG_UPDATE` - tells ConfigBuilder to never try to regenerate configs 
(for Docker images that handle config management itself)

