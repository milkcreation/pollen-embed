# Pollen Embed Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/embed/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.3-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

**Embed** Component.

## Installation

```bash
composer require pollen-solutions/embed
```

## Pollen Framework Setup

### Declaration

In config/app.php file.

```php
return [
      //...
      'providers' => [
          //...
          \Pollen\Embed\EmbedServiceProvider::class,
          //...
      ];
      // ...
];
```

### Standalone Declaration


### NPM Usage

In package.json file.

```json
{
  "dependencies": {
    "pollen-embed": "file:./vendor/pollen-solutions/embed"
  }
}
```


### Configuration

```php
// config/embed.php
// @see /vendor/pollen-solutions/embed/resources/config/embed.php
return [
      //...

      // ...
];
```
