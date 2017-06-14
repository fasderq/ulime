<?php
namespace Ulime\API\Model;


use Ulime\Backoffice\Article\Model\Article;

/**
 * Class Category
 * @package Ulime\API\Model
 */
class Category
{
    protected $title;
    protected $name;
    protected $articles;

    /**
     * Category constructor.
     * @param string $title
     * @param string $name
     * @param array $articles
     */
    public function __construct(
        string $title,
        string $name,
        array $articles = []
    ) {
        $this->title = $title;
        $this->name = $name;
        $this->articles = $articles;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Article[]
     */
    public function getArticles(): array
    {
        return $this->articles;
    }
}
