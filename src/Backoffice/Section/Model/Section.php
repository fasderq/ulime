<?php
namespace Ulime\Backoffice\Section\Model;

/**
 * Class Section
 * @package Ulime\Backoffice\Section\Model
 */
class Section
{
    protected $title;
    protected $name;
    protected $categories;

    /**
     * Section constructor.
     * @param string $title
     * @param string $name
     * @param array $categories
     */
    public function __construct(
        string $title,
        string $name,
        array $categories = []
    ) {
        $this->title = $title;
        $this->name = $name;
        $this->categories = $categories;
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
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}
