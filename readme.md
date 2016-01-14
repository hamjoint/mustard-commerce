## Mustard Commerce module

[![StyleCI](https://styleci.io/repos/45717240/shield?style=flat)](https://styleci.io/repos/45717240)
[![Build Status](https://travis-ci.org/hamjoint/mustard-commerce.svg)](https://travis-ci.org/hamjoint/mustard-commerce)
[![Total Downloads](https://poser.pugx.org/hamjoint/mustard-commerce/d/total.svg)](https://packagist.org/packages/hamjoint/mustard-commerce)
[![Latest Stable Version](https://poser.pugx.org/hamjoint/mustard-commerce/v/stable.svg)](https://packagist.org/packages/hamjoint/mustard-commerce)
[![Latest Unstable Version](https://poser.pugx.org/hamjoint/mustard-commerce/v/unstable.svg)](https://packagist.org/packages/hamjoint/mustard-commerce)
[![License](https://poser.pugx.org/hamjoint/mustard-commerce/license.svg)](https://packagist.org/packages/hamjoint/mustard-commerce)

Commerce support for [Mustard](http://withmustard.org/), the open source marketplace platform.

### Installation

#### Via Composer (using Packagist)

```sh
composer require hamjoint/mustard-commerce
```

Then add the Service Provider to config/app.php:

```php
Hamjoint\Mustard\Commerce\Providers\MustardCommerceServiceProvider::class
```

### Licence

Mustard is free and gratis software licensed under the [GPL3 licence](https://www.gnu.org/licenses/gpl-3.0). This allows you to use Mustard for commercial purposes, but any derivative works (adaptations to the code) must also be released under the same licence. Mustard is built upon the [Laravel framework](http://laravel.com), which is licensed under the [MIT licence](http://opensource.org/licenses/MIT).
