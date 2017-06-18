<?php
namespace Ulime\API\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\API\Model\Article;
use Ulime\API\Model\Category;

class ApiRepository
{
    protected $client;

    /**
     * ApiRepository constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        $data = $this->client
            ->selectCollection('ulime', 'categories')
            ->find()
            ->toArray();

        $categories = [];
        foreach ($data as $category) {
            $categories[] = $this->categoryToRow($this->documentToCategory($category));
        }

        return $categories;
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function getArticlesByCategory(string $name)
    {
        $data = $this->client
            ->selectCollection('ulime', 'categories')
            ->findOne(['name' => $name]);
        if ($data instanceof BSONDocument) {
            $category = $this->documentToCategory($data);
            if (!empty($category)) {
                $articleData = $category->getArticles();
                $articles = [];
                foreach ($articleData as $article) {
                    if ($article instanceof Article) {
                        $articles[] = $this->articleToRow($article);
                    } else {
                        throw new \Exception('undefined error');
                    }
                }
                return $articles;
            } else {
                throw new \Exception('not found');
            }
        } else {
            throw new \Exception('internal server error');
        }
    }

    /**
     * @param Category $category
     * @return array
     */
    protected function categoryToRow(Category $category): array
    {
        return [
            'title' => $category->getTitle(),
            'name' => $category->getName()
        ];
    }

    /**
     * @param BSONDocument $row
     * @return Category
     */
    protected function documentToCategory(BSONDocument $row): Category
    {
        $articles = [];
        foreach ($row->articles as $article) {
            $articles[] = $this->documentToArticle($article);
        }

        return new Category(
            $row->title,
            $row->name,
            $articles
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
            $document->body
        );
    }

    /**
     * @param Article $article
     * @return array
     */
    protected function articleToRow(Article $article): array
    {
        return [
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }
}
