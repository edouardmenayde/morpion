<?php

namespace Epic\Entities;

abstract class MarkModelType {
    const wizard = 'wizard';
    const warrior = 'warrior';
    const archer = 'archer';
}

class MarkModel
{
    public $id;
    public $name;
    public $icon;
    public $type;
}
