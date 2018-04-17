<?php

namespace  Ndexondeck\SoapBuilder;


class Payload extends Builder implements PayloadContract
{
    private $value = "";
    private $element_attributes = [];

    function __construct($value="",$element_attributes=[], $should_escape_value = true)
    {
        $this->value = $should_escape_value ? htmlspecialchars($value) : $value;
        $this->element_attributes = $element_attributes;
    }

    protected function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getElementAttributes()
    {
        return $this->element_attributes;
    }

}