<?php

namespace app\models;

use MongoDB\BSON\ObjectId;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for collection "books".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 */
class Book extends \yii\mongodb\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return ['test', 'books'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'date_writing',
            'short_description',
            'author_id',
            'date_created',
            'date_updated'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['author_id', 'yii\mongodb\validators\MongoIdValidator'],
            [['name', 'date_writing', 'author_id'], 'required'],
            [['name', 'short_description'], 'string'],
            ['author_id', 'exist', 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => '_id']],
            [['date_writing'], 'date', 'format' => 'yyyy.MM.dd']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'date_created',
                'updatedAtAttribute' => 'date_updated',
                'value' => date('Y.m.d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->author_id = new ObjectId($this->author_id);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['_id' => 'author_id']);
    }
}
