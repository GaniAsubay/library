<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for collection "authors".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 */
class Author extends \yii\mongodb\ActiveRecord
{

    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return ['test', 'authors'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'surname',
            'birthday',
            'biography',
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
            [['name', 'surname', 'birthday'], 'required'],
            [['name', 'surname', 'biography'], 'string'],
            [['birthday'], 'date', 'format' => 'yyyy.MM.dd']
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

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'surname', 'birthday', 'biography'];
        return $scenarios;
    }

    public function getBooks()
    {
        return $this->hasMany(Book::className(), ['author_id' => '_id'])->select(['name']);
    }
}
