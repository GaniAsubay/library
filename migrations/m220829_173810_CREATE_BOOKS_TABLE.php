<?php

class m220829_173810_CREATE_BOOKS_TABLE extends \yii\mongodb\Migration
{
    private $collection = 'books';

    public function up()
    {
        $this->createCollection($this->collection);
        $this->createIndex($this->collection, 'author_id');
    }

    public function down()
    {
        $this->dropIndex($this->collection, 'author_id');
        $this->dropCollection($this->collection);
    }
}
