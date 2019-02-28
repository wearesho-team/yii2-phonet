# Yii2 phonet api integration

Library for integration Phonet api with https protocol (active participant data exchange)

Uses `wearesho-team/phonet` to receive data of complete call

## Installation
```bash
composer require wearesho-team/yii2-phonet
```

## Structure

- [Controller](./src/Controller.php) - Handles requests from Phonet:
1) `Controller::handleClientRequest` - When service send request for client in your system;
2) `Controller::handleDial` - When service send event when call created;
3) `Controller::handleBridge` - When service send event when call bridged (client answered etc);
4) `Controller::handleHangup` - When service send event when call end. Create new [Job](./src/Job/Call/Complete/Receive.php) and put in to queue
 to receive data of complete call;
- [Bootstrap](./src/Bootstrap.php) - Configurations for app
- [Identity](./src/Identity.php) - Abstract class of identity (need to receive clients from cms):
    - Implements [IdentityInterface](./src/IdentityInterface.php);
    - Attributes and getters in [IdentityTrait](./src/IdentityTrait.php);
- [Call\Pause](./src/Call/Pause.php) - Enum that represent is call on pause or not;
- [Call\Type](./src/Call/Type.php) - Enum that represent is call internal or external;
- [Job\Call\Complete\Receive](./src/Job/Call/Complete/Receive.php) - Job to receive data of complete call to database
- Records:
    - [Call](./src/Record/Call.php) - Represent call
    - [Employee](./src/Record/Employee.php) - Represent operators in your system (contain `user_id` wish you can relate to your user table)
    - [Call\Complete\Data](./src/Record/Call/Complete/Data.php) - Represent data of call that already end
    - [Call\External\Data](./src/Record/Call/External/Data.php) - Represent data of external call
    - [Call\Internal\Data](./src/Record/Call/Internal/Data.php) - Represent data of internal call
- [Migrations](./src/Migrations) - need tables for package work

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
        $responsibleEmployeeInternalNumber = null,
        $responsibleEmployeeEmail = null
    ) {
        $this->url = $url;
        $this->name = $name;
        $this->urlText = $urlText;
        $this->responsibleEmployeeInternalNumber = $responsibleEmployeeInternalNumber;
        $this->responsibleEmployeeEmail = $responsibleEmployeeEmail;
    }

    public static function findBy(string $number,?string $trunk) : ?Phonet\Yii\IdentityInterface
    {
         // Receive user from your system by arguments
         
         return new static(
             $name,
             $url,
             $urlText,
             $responsibleEmployeeInternalNumber,
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
