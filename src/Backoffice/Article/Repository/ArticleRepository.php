<?php
namespace Ulime\Backoffice\Article\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Backoffice\Article\Model\Article;

/**
 * Class ArticleRepository
 * @package Ulime\Backoffice\Article\Repository
 */
class ArticleRepository
{
    protected $client;

    /**
     * ArticleRepository constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $name
     * @return Article
     * @throws \Exception
     */
    public function getArticleByName(string $name): Article
    {
        $article = $this->client
            ->selectCollection('ulime', 'articles')
            ->findOne(['name' => $name]);

        if (!empty($article)) {
            if ($article instanceof BSONDocument) {
                return $this->documentToArticle($article);
            } else {
                throw new \Exception('db error');
            }
        } else {
            throw new \Exception('article not found');
        }
    }

    /**
     * @return Article[]
     */
    public function getArticles()
    {
        $data = $this->client
            ->selectCollection('ulime', 'articles')
            ->find()->toArray();

        $articles = [];
        foreach ($data as $article) {
            $articles[] = $this->documentToArticle($article);
        }

        return $articles;
    }

    /**
     * @param Article $article
     */
    public function addArticle(Article $article): void
    {
        $this->client
            ->selectCollection('ulime', 'articles')
            ->insertOne($this->articleToRow($article));
    }

    /**
     * @param Article $article
     * @param string $name
     */
    public function editArticle(Article $article, string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'articles')
            ->findOneAndUpdate(['name' => $name], [
                '$set' => $this->articleToRow($article)
            ]);

        $this->client
            ->selectCollection('ulime', 'categories')
            ->updateMany(
                [
                    'articles' => [
                        '$elemMatch' => [
                            'name' => $name
                        ]
                    ]
                ],
                [
                    '$set' => ['articles.$'  => $this->articleToRow($article)]
                ],
                ['multi' => true]
            );
    }

    /**
     * @param string $name
     */
    public function deleteArticle(string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'articles')
            ->deleteOne(['name' => $name]);

        $this->client
            ->selectCollection('ulime', 'categories')
            ->updateMany(
                [],
                [
                    '$pull' => [
                        'articles' => [
                            'name' => $name
                        ]
                    ]
                ],
                ['multi' => true]
            );
    }

    /**
     * @param BSONDocument $row
     * @return Article
     */
    public function documentToArticle(BSONDocument $row): Article
    {
        return new Article(
            $row->name,
            $row->title,
            $row->label,
            $row->body,
            $row->img
        );
    }

    /**
     * @param Article $article
     * @return array
     */
    public function articleToRow(Article $article): array
    {
        return [
            'name' => $article->getName(),
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody(),
            'img' => $article->getImage()
        ];
    }
}
