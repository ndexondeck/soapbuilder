<?php

namespace Ndexondeck\SoapBuilder;


class Builder
{
    private $key;

    private $namespaces = [];

    private $attributes = [];

    private $names = [];

    private $properties = [];

    private $version;

    private $xml_mode = false;

    private $response = false;

    function __construct($key='soap', $namespaces=[],$version='1.1')
    {
        $this->key = $key;
        $this->namespaces = $namespaces;
        $this->version = $version;
    }

    function setAsXml(){
        $this->xml_mode = true;
        return $this;
    }

    function addAttribute($namespace,$href){
        $this->namespaces[$namespace] = $href;
    }

    public function __get($name)
    {
        $name = str_replace("__",":",$name);

        if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name];
        }
        return null;
    }

    public function __set($name, PayloadContract $value)
    {
//        dump($value);
        $name = str_replace("__",":",$name);

        $v = explode(":",$name);

        $i = 0;
        if(isset($v[1])){
            $i = 1;$this->names[] = $v[0];
        }
        $this->properties[] = $v[$i];

        if($value instanceof PayloadCollection){
            $this->attributes[$name] = $value;
        }
        else{
            $this->attributes[$name] = $value;
        }

    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    private function validateNamespace($names){
        $namespaces = array_keys($this->namespaces);
        $namespaces[] = $this->key;

        if($v = array_diff($names,$namespaces)) throw new \Exception('Unknown XML NameSpace : '.implode(', ',$v));
    }

    private function validateStructure(){
        if(in_array('Header',$this->properties)){
            if(!in_array('Body',$this->properties)) throw new \Exception("Body was not found in xml");
        }else{
            throw new \Exception("Header was not found in xml");
        }
    }

    public function getXml(){
        if(!$this->xml_mode){
            $this->validateStructure();
            $this->validateNamespace($this->names);

            $moreAttr = "";
            foreach($this->namespaces as $key=>$value) $moreAttr .= " xmlns:$key=\"$value\"";

            switch ($this->version){
                case "1.2":
                    $schema= "http://www.w3.org/2003/05/soap-envelope";
                    break;
                default:
                    $schema = "http://schemas.xmlsoap.org/soap/envelope/";
                    break;
            }
        }

        $xmlContent = $this->readPayload($this->attributes);

        $prepend = "";
        if($this->isResponse()){
            $prepend = '<?xml version="'.$this->version.'" encoding="utf-8" ?>';
        }

        if(!$this->xml_mode){
            return <<<XML
{$prepend}
<{$this->key}:Envelope xmlns:{$this->key}="$schema"{$moreAttr}>
   {$xmlContent}
</{$this->key}:Envelope>
XML;
        }

        return <<<XML
{$prepend}        
{$xmlContent}
XML;
    }

    private function recursiveLookup(PayloadContract $payload, $tag=null){

        if(!$this->xml_mode) $this->validateNamespace($payload->names);

        if(count($payload->attributes) > 0) return $this->readPayload($payload->attributes,$tag);

        return $payload->getValue();
    }

    private function readPayload($attributes,$tagged=null)
    {
        $xmlContent = "";
        foreach($attributes as $tag=>$payload){

            if($tagged) $tag = $tagged;

            if($payload instanceof PayloadCollection){
                $content = $this->recursiveLookup($payload,$payload->getChild());
            }
            else{
                $content = $this->recursiveLookup($payload);
            }

            $element_attribute = $this->parseElementAttributes($payload->getElementAttributes());

            if($content == "" or is_null($content)) $xmlContent .= "<$tag"."$element_attribute/>";
            else{
                $xmlContent .= "<$tag"."$element_attribute>";
                $xmlContent .= $content;
                $xmlContent .= "</$tag>";
            }
        }
        return $xmlContent;
    }

    /**
     * @return Builder
     */
    public function setAsResponse()
    {
        $this->response = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    private function parseElementAttributes($elementAttributes)
    {
        $element_attribute = "";
        $element_attributes = [];
        foreach($elementAttributes as $name=>$value){
            $element_attributes[] = $name.'="'.$value.'"';
        }

        if(!empty($element_attributes)) $element_attribute = " ".implode(" ",$element_attributes);

        return $element_attribute;
    }

    /**
     * @return bool
     */
    public function isResponse()
    {
        return $this->response;
    }

    /**
     * @param string $version
     * @return Builder
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

}