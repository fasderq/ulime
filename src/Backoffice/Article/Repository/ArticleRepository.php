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
            $articles[] = $this->rowToArticle($article);
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
    }

    /**
     * @param string $name
     */
    public function deleteArticle(string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'articles')
            ->deleteOne(['name' => $name]);
    }

    /**
     * @param BSONDocument $row
     * @return Article
     */
    public function rowToArticle(BSONDocument $row): Article
    {
        return new Article(
            $row->name,
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
            'name' => $article->getName(),
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }
}
