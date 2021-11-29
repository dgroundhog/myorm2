<?php

/**
 * Class MyField
 *
 */
class MyFieldTiny  implements MyBase
{


    /**
     * 唯一码
     * @var string
     */
    public $uuid = "uuid";

    /**
     * 字段位置
     * @var int
     */
    public $position = 255;


    /**
     * 第一次保存时冗余，后续根据实际的UUID更新
     * @var bool
     */
    public $name = "";


    /**
     * MyField constructor.
     */
    public function __construct()
    {
        //$this->scope = "FIELD";
    }


    function getAsArray()
    {
        $a_data = array();
        $a_data['uuid'] = $this->uuid;
        $a_data['name'] = $this->name;
        $a_data['uuid'] = $this->position;
        return $a_data;
    }

    function parseToObj($a_data)
    {


        $this->uuid = $a_data['uuid'];
        $this->name = $a_data['name'];
        $this->position = $a_data['position'];

        return $this;
    }

    function init($v1)
    {
        // TODO: Implement init() method.

        //外部生产
    }
}