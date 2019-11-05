# Introduction

Laravel assets optimize include cdn, web assets cache buster

Compatible and tested with Laravel `5.1+`

# Installation

## Laravel 5.1+

At `composer.json` of your Laravel installation, add the following require line:

```
{
    "require": {
        "edwin404/laravel-smart-assets": "master-dev"
    }
}
```

Run composer update to add the package to your Laravel app.

At `config/app.php`, add the Service Provider:

```
'providers' => [
    // ...
    Edwin404\SmartAssets\Providers\SmartAssetsServiceProvider:class,
]
```

# Quick start

## Using with smart-assets

Blade code:

CODE:1
```
<script src="@assets('path/to/app.js')"></script>
<img src="@assets('path/to/img.png')" />
<link type="text/css" src="@assets('path/to/app.css')" />
```

will compile to :

CODE:2
```
<script src="/path/to/app.js?v160909152021"></script>
<img src="/path/to/img.png?v160909132534" />
<link type="text/css" src="/path/to/app.css?v160909133706" />
```

## Use CDN

Copy `vender/edwin404/laravel-smart-assets/config/smart-assets.php` to `config/smart-assets.php`

Specify the `assets_cdn` config

```
<?php
return [
    // ...
    'assets_cdn' => 'http://cdn.edwin404.com/',
];
```

CODE:1 will comple to :
```
<script src="http://cdn.edwin404.com/path/to/app.js?v160909152021"></script>
<img src="http://cdn.edwin404.com/path/to/img.png?v160909132534" />
<link type="text/css" src="http://cdn.edwin404.com/path/to/app.css?v160909133706" />
```

# Support

Found a bug? Please create an issue on the [GitHub](https://github.com/edwin404/laravel-smart-assets) project page or send a pull request if you have a fix or extension.

You can also send me a message at edwin404@163.com to discuss more obscure matters about the component.

# License

Licensed under the The MIT License (MIT). Please see LICENSE for more information.