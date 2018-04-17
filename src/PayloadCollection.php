<?php

namespace  Ndexondeck\SoapBuilder;

class PayloadCollection extends Builder implements PayloadContract
{

    private $element_attributes = [];
    private $child;
    private $child_element_attributes;

    function __construct($child,$element_attributes=[])
    {
        $this->child = $child;
        $this->element_attributes = $element_attributes;
    }

    public function __set($name, PayloadContract $value)
    {
        if($this->child !== $name) throw new \Exception("Cannot override soap payload collection child name from {$this->child} to $name");

        $this->pushValue($value);
    }

    protected function pushValue(PayloadContract $value){
        $attributes = $this->getAttributes();
        $attributes[] = $value;
        $this->setAttributes($attributes);
    }

    /**
     * @param string $value
     * @param array $element_attribute
     * @param bool $studly
     */
    function append($value="",$element_attribute=[],$studly=true){

        if(!is_array($value)) {
            $this->pushValue(new Payload($value,$element_attribute));
            return;
        }

        if(array_values($value) == $value){
            foreach ($value as $v) $this->pushValue(new Payload($v,$element_attribute));
            return;
        }

        $this->setItem($value,$element_attribute,$studly);
    }

    /**
     * @param integer|null $index
     * @param string $value
     * @param array $element_attribute
     * @return Payload
     * @throws \Exception
     */
    function item($index=null,$value="",$element_attribute=[]){

        if(is_null($index)) $this->pushValue(new Payload($value,$element_attribute));

        $attributes = $this->getAttributes();

        if(is_null($index)) $index = count($attributes) - 1;
        elseif(!isset($attributes[$index])) throw new \Exception("Undefined index $index in soap payload collection");

        return $attributes[$index];
    }

    /**
     * @param array $array
     * @param array $element_attribute
     * @param bool $studly
     * @return Payload
     */
    function setItem(array $array,$element_attribute=[],$studly=true){

        $payload = new Payload('',$element_attribute);

        foreach ($array as $key=>$value){
            if($studly) $key = studly_case($key);
            $payload->{$key} = new Payload($value);
        }

        $this->pushValue($payload);
    }

    public function getChild()
    {
        return $this->child;
    }

    /**
     * @param $element_attributes
     */
    public function setElementAttributes($element_attributes)
    {
        $this->element_attributes = $element_attributes;
    }

    /**
     * @return array
     */
    public function getElementAttributes()
    {
        return $this->element_attributes;
    }

    /**
     * @return array
     */
    public function getChildElementAttributes()
    {
        return $this->child_element_attributes;
    }

    protected function getValue()
    {
        return "";
    }
}