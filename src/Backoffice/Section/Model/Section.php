<?php
namespace Ulime\Backoffice\Section\Model;


class Section
{
    protected $title;
    protected $name;
    protected $categories;

    public function __construct(
        string $title,
        string $name,
        array $categories = []
    ) {
        $this->title = $title;
        $this->name = $name;
        $this->categories = $categories;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
