<?php

namespace app\controllers;

use app\models\Book;
use Yii;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: gani
 * Date: 8/30/22
 * Time: 6:13 PM
 */
class BooksController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            Yii::$app->response->statusCode = 200;
            return Yii::$app->mongodb->getCollection('books')->aggregate([
                [
                    '$lookup' => [
                        'from' => 'authors',
                        'localField' => 'author_id',
                        'foreignField' => '_id',
                        'as' => 'author'
                    ]
                ],
                ['$unwind' => '$author'],
                [
                    '$project' => [
                        '_id' => 0,
                        'name' => 1,
                        'date_writing' => 1,
                        'author' => ['$concat' => ['$author.name', ' ', '$author.surname']]
                    ]
                ],
                [
                    '$sort' => [
                        'date_writing' => -1
                    ]
                ]

            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionView()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $bookId = Yii::$app->request->get('id');
        try {
            Yii::$app->response->statusCode = 200;
            return Yii::$app->mongodb->getCollection('books')->aggregate([
                [
                    '$match' => [
                        '_id' => $bookId,
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'authors',
                        'localField' => 'author_id',
                        'foreignField' => '_id',
                        'as' => 'author'
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 1,
                        'name' => 1,
                        'date_writing' => 1,
                        'short_description' => 1,
                        'date_created' => 1,
                        'date_updated' => 1,
                        'author' => [
                            'name' => 1,
                            'surname' => 1,
                            'birthday' => 1,
                            'biography' => 1,
                        ],
                    ]
                ],

            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $bookId = Yii::$app->request->get('id');
        Yii::$app->response->statusCode = 204;
        Book::deleteAll(['_id' => $bookId]);
    }
}