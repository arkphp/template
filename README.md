# Native PHP template engine

## Usage

```php
$template = new Template([
    'root' => '/path/to/templates/root',
]);

$template->render('index.php', [
    'username' => 'hello'
]);
```

Layout:

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php $this->block('title');?></title>
</head>
<body>
    <?php $this->block('header');?>
    <?php $this->beginBlock('block');?>content<?php $this->endBlock();?>
    <?php $this->begin('content');?><?php $this->end();?>
</body>
</html>
```

## Template Functions

```
extend
block
begin
end
filter($v, 'strtolower|xxx')
escape
```