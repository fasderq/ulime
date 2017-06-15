<?php
namespace Ulime\Backoffice\Category\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Backoffice\Article\Model\Article;
use Ulime\Backoffice\Category\Model\Category;

/**
 * Class CategoryRepository
 * @package Ulime\Backoffice\Category\Repository
 */
class CategoryRepository
{
    protected $client;

    /**
     * CategoryRepository constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $name
     * @return Category
     * @throws \Exception
     */
    public function getCategoryByName(string $name): Category
    {
        $category = $this->client
            ->selectCollection('ulime', 'categories')
            ->findOne(['name' => $name]);

        if (!empty($category)) {
            if ($category instanceof BSONDocument) {
                return $this->rowToCategory($category);
            } else {
                throw new \Exception('db error');
            }
        } else {
            throw new \Exception('category no found');
        }
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        $data = $this->client
            ->selectCollection('ulime', 'categories')
            ->find()
            ->toArray();

        $categories = [];

        foreach ($data as $category) {
            $categories[] = $this->rowToCategory($category);
        }

        return $categories;
    }

    /**
     * @param Category $category
     */
    public function addCategory(Category $category): void
    {
        $this->client
            ->selectCollection('ulime', 'categories')
            ->insertOne($this->categoryToRow($category));
    }

    /**
     * @param Category $category
     * @param string $name
     */
    public function editCategory(Category $category, string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'categories')
            ->findOneAndUpdate(['name' => $name], [
                '$set' => $this->categoryToRow($category)
            ]);
    }

    /**
     * @param string $name
     */
    public function deleteCategory(string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'categories')
            ->deleteOne(['name' => $name]);
    }

    /**
     * @param BSONDocument $row
     * @return Category
     */
    protected function rowToCategory(BSONDocument $row): Category
    {
        $articles = [];
        foreach ($row->articles as $article) {
            $articles[] = $this->rowToArticle($article);
        }

        return new Category(
            $row->title,
            $row->name,
            $articles
        );
    }

    /**
     * @param BSONDocument $row
     * @return Article
     */
    protected function rowToArticle(BSONDocument $row): Article
    {
        return new Article(
            $row->name,
            $row->title,
            $row->label,
            $row->body
        );
    }

    /**
     * @param Category $category
     * @return array
     */
    protected function categoryToRow(Category $category): array
    {
        $articles = [];
        foreach ($category->getArticles() as $articleId => $article) {
            $articles[$articleId] = $this->articleToRow($article);
        }

        return [
            'title' => $category->getTitle(),
            'name' => $category->getName(),
            'articles' => $articles
        ];
    }

    /**
     * @param Article $article
     * @return array
     */
    protected function articleToRow(Article $article): array
    {
        return [
            'name' => $article->getName(),
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }

}
