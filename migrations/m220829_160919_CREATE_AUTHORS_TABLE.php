<?php

class m220829_160919_CREATE_AUTHORS_TABLE extends \yii\mongodb\Migration
{
    private $collection = 'authors';

    public function up()
    {
        $this->createCollection($this->collection);
    }

    public function down()
    {
        $this->dropCollection($this->collection);
    }
}
