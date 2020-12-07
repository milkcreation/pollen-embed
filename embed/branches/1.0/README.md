# Pollen Solutions Embed Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://svn.tigreblanc.fr/pollen-solutions/embed/tags/1.0.0)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

**Embed** Component.

## Installation

```bash
composer require pollen-solutions/embed
```

## Setup

### Declaration

```php
// config/app.php
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

### Configuration

```php
// config/embed.php
// @see /vendor/pollen-solutions/embed/config/embed.php
return [
      //...

      // ...
];
```
