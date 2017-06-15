<?php
namespace Ulime\Frontoffice\General\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Frontoffice\General\Model\Article;

/**
 * Class GeneralRepository
 * @package Ulime\Frontoffice\General\Repository
 */
class GeneralRepository
{
    protected $client;

    /**
     * GeneralRepository constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @return Article[]
     */
    public function getPopularArticles()
    {
        $data = $this->client
            ->selectCollection('ulime', 'articles')
            ->find([], ['sort' => ['metrics.count' => -1]])->toArray();

        $articles = [];
        foreach ($data as $article) {
            $articles[] = $this->documentToArticle($article);
        }

        return $articles;
    }


    /**
     * @param BSONDocument $document
     * @return Article
     */
    public function documentToArticle(BSONDocument $document): Article
    {
        return new Article(
            $document->title,
            $document->label,
            $document->body,
            $document->metrics->count ?? null
        );
    }
}
