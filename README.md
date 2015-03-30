# PHP Template [![Build Status](https://travis-ci.org/arkphp/template.png)](https://travis-ci.org/arkphp/template)

Native PHP template engine

## Installation

```
composer require ark/template
```

## Usage

```php
use Ark\Template\Engine;

$template = new Engine('/path/to/templates/root');

$template->render('index.php', [
    'username' => 'hello'
]);
```

layout.php:

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php $this->block('title');?></title>
</head>
<body>
    <?php $this->block('header');?>
    <?php $this->begin('content');?><?php $this->end();?>
    <?php $this->block('footer');?>
</body>
</html>
```

index.php:

```php
<?php $this->extend('layout.php');?>
<?php $this->begin('title');?>Page Title<?php $this->end();?>
<?php $this->begin('content');?>
Page Content
<?php $this->end();?>

<?php $this->begin('footer');?>
Custom footer
<?php $this->end();?>
```

## Markups

Declare layout:

```php
<?php $this->extend('layout.php');?>
```

Declare a block:

```php
<!-- empty block -->
<?php $this->block('blockname');?>

<!-- block with content -->
<?php $this->begin('blockname');?>
Block content
<?php $this->end();?>
```

Include another template:

```php
<?php $this->render('another.php');?>
```

## Template Functions

Escaping:

```php
<?=$this->escape($username)?>
<!-- or for short -->
<?=$this->e($username)?>
```

Filter:

```php
<?=$this->filter($username, 'strtolower|trim')?>
<?=$this->e($username, 'strtolower|trim');?>
```