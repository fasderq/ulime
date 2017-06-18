<?php
namespace Ulime\Frontoffice\General\Model;


class Section
{
    protected $name;
    protected $title;

    public function __construct(
        string $name,
        string $title
    ) {
        $this->name = $name;
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
