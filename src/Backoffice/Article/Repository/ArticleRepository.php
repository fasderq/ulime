<?php
namespace Ulime\Backoffice\Article\Repository;


use MongoDB\BSON\ObjectID;
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
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $id
     * @return Article
     * @throws \Exception
     */
    public function getArticleById(string $id): Article
    {
        $article = $this->client
            ->selectCollection('ulime', 'articles')
            ->findOne(['_id' => new ObjectID($id)]);

        if (!empty($article)) {
            if ($article instanceof BSONDocument) {
                return $this->rowToArticle($article);
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
            $ids = get_object_vars($article->_id);
            $articles[$ids['oid']] = $this->rowToArticle($article);
        }

        return $articles;
    }

    public function addArticle(Article $article): void
    {
        $this->client
            ->selectCollection('ulime', 'articles')
            ->insertOne($this->articleToRow($article));
    }

    /**
     * @param BSONDocument $row
     * @return Article
     */
    public function rowToArticle(BSONDocument $row): Article
    {
        return new Article(
            $row->title,
            $row->label,
            $row->body
        );
    }

    /**
     * @param Article $article
     * @return array
     */
    public function articleToRow(Article $article): array
    {
        return [
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }
}
