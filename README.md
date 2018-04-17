# Soap Builder

Soap builder is a php OOP library that can help you build any form of XML string with so much ease and clarity. It is mostly powered by php magic methods, as it uses them to intuitively setup XML tags.

[![Total Downloads](https://poser.pugx.org/ndexondeck/soapbuilder/downloads.svg)](https://packagist.org/packages/ndexondeck/lauditor)


## Installation

**Install with Composer:**

```
composer require ndexondeck/soapbuilder
```

## Documents will be available soon, but for now see a few examples

<li><h3>Build a Simple Soap Request</h3></li>
   
```php
$soapBuilder = new Builder();

$soapBuilder->soap__Header = new SoapPayload();

$soapBuilder->Body = new SoapPayload();
$soapBuilder->Body->Username = new SoapPayload('ndxondeck@gmail.com');
$soapBuilder->Body->Password = new SoapPayload('ndex4Jesus');

echo $soapBuilder->getXml();
```


<li><h3>Build a more complex Soap request</h3></li>
   
```php
 $soapBuilder = new Builder('soap',[
        "tem"=>"http://tempuri.org/",
        "sms"=>"http://schemas.datacontract.org/2004/07/SMSAppws",
        "wsa"=>"http://schemas.xmlsoap.org/ws/2004/08/addressing",
    ],'1.2');

$soapBuilder->soap__Header = new SoapPayload();
$soapBuilder->soap__Header->wsa__Action = new SoapPayload('http://tempuri.org/IService/SendMessage',[
    "xmlns:wsa"=>"http://www.w3.org/2005/08/addressing"
]);
$soapBuilder->soap__Header->wsa__To = new SoapPayload('https://sms.sender.example/Service.svc',[
    "xmlns:wsa"=>"http://www.w3.org/2005/08/addressing"
]);

$soapBuilder->soap__Body = new SoapPayload();
$soapBuilder->soap__Body->tem__SendMessage = new SoapPayload();
$soapBuilder->soap__Body->tem__SendMessage->tem__message = new SoapPayload();
$soapBuilder->soap__Body->tem__SendMessage->tem__message->sms__Message = new SoapPayload($msg);
$soapBuilder->soap__Body->tem__SendMessage->tem__message->sms__MobileNo = new SoapPayload($phone);

echo $soapBuilder->getXml();
```


<li><h3>Build a simple XML string</h3></li>
   
```php
$xmlBuilder = (new SoapBuilder())->setAsXml()->setVersion('1.0');
$xmlBuilder->SearchCriteria = new SoapPayload();
$xmlBuilder->SearchCriteria->UserName = new SoapPayload('John');

echo $xmlBuilder->getXml();
```


<li><h3>Build a more complex XML string</h3></li>
   
```php
$xmlBuilder = new SoapBuilder();
$xmlBuilder->setVersion('1.0')->setAsResponse()->setAsXml();
$xmlBuilder->Response = new SoapPayload();
$xmlBuilder->Response->ResponseCode = new SoapPayload('00');
$xmlBuilder->Response->UserList = new SoapPayloadCollection('Department');

$user_count = 0;
if(!empty($results)){
    foreach ($results as $department){

        $collection = new SoapPayloadCollection('User',['Id'=>$department['id'], 'Name'=>$department['name']]);

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