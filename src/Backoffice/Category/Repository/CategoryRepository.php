<?php
namespace Ulime\Backoffice\Category\Repository;


use MongoDB\BSON\ObjectID;
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
     * @param string $id
     * @return Category
     * @throws \Exception
     */
    public function getCategoryById(string $id): Category
    {
        $category = $this->client
            ->selectCollection('ulime', 'categories')
            ->findOne(['_id' => new ObjectID($id)]);

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
            $categories[get_object_vars($category->_id)['oid']] = $this->rowToCategory($category);
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
     * @param string $id
     */
    public function editCategory(Category $category, string $id): void
    {
        $this->client
            ->selectCollection('ulime', 'categories')
            ->findOneAndUpdate(['_id' => new ObjectID($id)], [
                '$set' => $this->categoryToRow($category)
            ]);
    }

    /**
     * @param string $id
     */
    public function deleteCategory(string $id): void
    {
        $this->client
            ->selectCollection('ulime', 'categories')
            ->deleteOne(['_id' => new ObjectID($id)]);
    }

    /**
     * @param BSONDocument $row
     * @return Category
     */
    protected function rowToCategory(BSONDocument $row): Category
    {
        $articles = [];
        foreach ($row->articles as $articleId => $article) {
            $articles[$articleId] = $this->rowToArticle($article);
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
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }

}
