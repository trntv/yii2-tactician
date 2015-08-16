Yii2 Tactician
==============
[Tactician](https://github.com/thephpleague/tactician) is a simple, flexible command bus. 
This package provide it's very basic integration with Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require trntv/yii2-tactician
```

or add

```
"trntv/yii2-tactician": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Somewhere in your config:
```php
'components' => [
    ...
    'commandBus' => [
        'class' => '\trntv\tactician\Tactician',
        'commandNameExtractor' => '\League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor'
        'methodNameInflector' => '\League\Tactician\Handler\MethodNameInflector\HandleInflector'
        'commandToHandlerMap' => [
            'app\commands\command\SendEmailCommand' => 'app\commands\handler\SendEmailHandler'
        ],
        'middlewares' => [
            ...
        ]
    ]
    ...
]
```

Somewhere in your app:
```php
Yii::$app->commandBus->handle(new SendEmailCommand([
    'from' => 'email@example.org',
    'to' => 'user@example.org',
    'body' => '...'
]))

Yii::$app->commandBus->handleMultiply([
    new SendEmailCommand([
        'from' => 'email@example.org',
        'to' => 'user@example.org',
        'body' => '...'
    ]),
    new SomeOtherCommand([
        ...
    ])
])
```
