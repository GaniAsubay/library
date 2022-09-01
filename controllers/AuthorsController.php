<?php

namespace app\controllers;

use app\models\Author;
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
class AuthorsController extends ActiveController
{
    public $modelClass = 'app\models\Author';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['statistic'] = ['GET'];
        return $verbs;
    }

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
                'scenario' => Author::SCENARIO_UPDATE,
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $authors = Author::find()->select(['name', 'surname', 'birthday'])->orderBy('date_updated DESC')->asArray()->all();
        Yii::$app->response->statusCode = 200;
        return $authors;
    }

    public function actionView()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $authorId = Yii::$app->request->get('id');
        try {
            Yii::$app->response->statusCode = 200;
            return Yii::$app->mongodb->getCollection('authors')->aggregate([
                [
                    '$match' => [
                        '_id' => $authorId,
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'books',
                        'localField' => '_id',
                        'foreignField' => 'author_id',
                        'as' => 'books'
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 1,
                        'name' => 1,
                        'surname' => 1,
                        'birthday' => 1,
                        'biography' => 1,
                        'date_created' => 1,
                        'date_updated' => 1,
                        'books' => [
                            'name' => 1,
                            'date_writing' => 1
                        ],
                    ]
                ]

            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $authorId = Yii::$app->request->get('id');
        Yii::$app->response->statusCode = 204;
        Book::deleteAll(['author_id' => $authorId]);
        Author::deleteAll(['_id' => $authorId]);
    }

    public function actionStatistic()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            Yii::$app->response->statusCode = 200;
            return Yii::$app->mongodb->getCollection('authors')->aggregate([
                [
                    '$lookup' => [
                        'from' => 'books',
                        'localField' => '_id',
                        'foreignField' => 'author_id',
                        'as' => 'books'
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'name' => 1,
                        'surname' => 1,
                        'books_count' => [
                            '$size' => '$books'
                        ],
                    ]
                ]

            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }
}