<?php
namespace Ulime\Frontoffice\General\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Frontoffice\General\Model\Article;
use Ulime\Frontoffice\General\Model\Section;

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
    public function getPopularArticles(): array
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


    public function getSections(): array
    {
        $data = $this->client
            ->selectCollection('ulime', 'section')
            ->find()
            ->toArray();

        $sections = [];
        foreach ($data as $section) {
            $sections[] = $this->documentToSection($section);
        }

        return $sections;
    }

    /**
     * @param BSONDocument $document
     * @return Section
     */
    protected function documentToSection(BSONDocument $document): Section
    {
        return new Section(
            $document->name,
            $document->title
        );
    }
    /**
     * @param BSONDocument $document
     * @return Article
     */
    protected function documentToArticle(BSONDocument $document): Article
    {
        return new Article(
            $document->title,
            $document->label,
            $document->body,
            $document->metrics->count ?? null
        );
    }
}
