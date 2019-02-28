# Yii2 phonet api integration

Library for integration Phonet api with https protocol (active participant data exchange)

Uses `wearesho-team/phonet` to receive data of complete call

## Installation
```bash
composer require wearesho-team/yii2-phonet
```

## Structure

- [Controller](./src/Controller.php) - Handles requests from Phonet
- [Bootstrap](./src/Bootstrap.php) - Main 

## Usage

### Bootstrap

```php
<?php

return [
    'bootstrap' => [
        'phonet' => [
            'class' => \Wearesho\Phonet\Yii\Bootstrap::class,
        ]
    ]
];
```

### Controller

```php
<?php

return [
    'controllerMap' => [
        'phonet' => [
            'class' => \Wearesho\Phonet\Yii\Controller::class,
            'identity' => \Wearesho\Phonet\Yii\IdentityInterface::class,
            'repository' => \Wearesho\Phonet\Repository::class,
            'queue' => \yii\queue\Queue::class,
        ]
    ]
];
```

### Identity

Implement [IdentityInterface](./src/IdentityInterface.php) to your user/client model to receiving needs data for [Controller](./src/Controller.php)

Example:

```php
<?php

use Wearesho\Phonet;

class User implements Phonet\Yii\IdentityInterface
{
    use Phonet\Yii\IdentityTrait;
    
    public function __construct(
        $url, $urlText,
        $name = null,
        $responsibleEmployeeExt = null,
        $responsibleEmployeeEmail = null
    ) {
        $this->url = $url;
        $this->name = $name;
        $this->urlText = $urlText;
        $this->responsibleEmployeeExt = $responsibleEmployeeExt;
        $this->responsibleEmployeeEmail = $responsibleEmployeeEmail;
    }

    public static function findBy(string $number,?string $trunk) : ?Phonet\Yii\IdentityInterface
    {
         // Receive user from your system by arguments
         
         return new static(
             $name,
             $url,
             $urlText,
             $responsibleEmployeeExt,
             $responsibleEmployeeEmail
         );
    }
}
```

### Queue

When Phonet service send event with status that call is end, controller will add to queue [Job\ReceiveCompleteCall](src/Job/Call/Complete/Receive.php).

It will fetch data of that call and save to database

## Authors
- [Roman Varkuta](mailto:roman.varkuta@gmail.com)

## License
- [MIT](./LICENSE)
