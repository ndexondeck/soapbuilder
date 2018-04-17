# Soap Builder

Soap builder is a php OOP library that can help you build any form of XML string with so much ease and clarity. It is mostly powered by php magic methods, as it uses them to intuitively setup XML tags.

[![Total Downloads](https://poser.pugx.org/ndexondeck/soapbuilder/downloads.svg)](https://packagist.org/packages/ndexondeck/lauditor)


## Installation

**Install with Composer:**

```
composer require ndexondeck/soapbuilder
```

## Documents will be available soon, but for now see a few examples

<h3><li>Build a Simple Soap Request</li></h3>
   
```php
$soapBuilder = new Builder();

$soapBuilder->soap__Header = new Payload();

$soapBuilder->Body = new Payload();
$soapBuilder->Body->Username = new Payload('ndxondeck@gmail.com');
$soapBuilder->Body->Password = new Payload('ndex4Jesus');

echo $soapBuilder->getXml();
```


<h3><li>Build a more complex Soap request</li></h3>
   
```php
 $soapBuilder = new Builder('soap',[
        "tem"=>"http://tempuri.org/",
        "sms"=>"http://schemas.datacontract.org/2004/07/SMSAppws",
        "wsa"=>"http://schemas.xmlsoap.org/ws/2004/08/addressing",
    ],'1.2');

$soapBuilder->soap__Header = new Payload();
$soapBuilder->soap__Header->wsa__Action = new Payload('http://tempuri.org/IService/SendMessage',[
    "xmlns:wsa"=>"http://www.w3.org/2005/08/addressing"
]);
$soapBuilder->soap__Header->wsa__To = new Payload('https://sms.sender.example/Service.svc',[
    "xmlns:wsa"=>"http://www.w3.org/2005/08/addressing"
]);

$soapBuilder->soap__Body = new Payload();
$soapBuilder->soap__Body->tem__SendMessage = new Payload();
$soapBuilder->soap__Body->tem__SendMessage->tem__message = new Payload();
$soapBuilder->soap__Body->tem__SendMessage->tem__message->sms__Message = new Payload($msg);
$soapBuilder->soap__Body->tem__SendMessage->tem__message->sms__MobileNo = new Payload($phone);

echo $soapBuilder->getXml();
```


<h3><li>Build a simple XML string</li></h3>
   
```php
$xmlBuilder = (new SoapBuilder())->setAsXml()->setVersion('1.0');
$xmlBuilder->SearchCriteria = new Payload();
$xmlBuilder->SearchCriteria->UserName = new Payload('John');

echo $xmlBuilder->getXml();
```


<h3><li>Build a more complex XML string</li></h3>
   
```php
$xmlBuilder = new SoapBuilder();
$xmlBuilder->setVersion('1.0')->setAsResponse()->setAsXml();
$xmlBuilder->Response = new Payload();
$xmlBuilder->Response->ResponseCode = new Payload('00');
$xmlBuilder->Response->UserList = new PayloadCollection('Department');

$user_count = 0;
if(!empty($results)){
    foreach ($results as $department){

        $collection = new PayloadCollection('User',['Id'=>$department['id'], 'Name'=>$department['name']]);

        $this_count = 0;
        foreach ($department['users'] as $user){
            $collection->append($user,[],true);
            $user_count++;
            $this_count++;
        }

        if($this_count > 0){
            $xmlBuilder->Response->UserList->Department = $collection;
        }
    }
}

$xmlBuilder->Response->UserList->setElementAttributes(['TotalAvailable'=>$user_count]);

echo $xmlBuilder->getXml();
```