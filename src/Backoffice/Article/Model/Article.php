<?php
namespace Ulime\Backoffice\Article\Model;

/**
 * Class Article
 * @package Ulime\Backoffice\Article\Model
 */
class Article
{
    protected $name;
    protected $title;
    protected $label;
    protected $body;

    /**
     * Article constructor.
     * @param string $name
     * @param string $title
     * @param string $label
     * @param string $body
     */
    public function __construct(
        string $name,
        string $title,
        string $label,
        string $body
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->label = $label;
        $this->body = $body;
    }

    public function getName(): string
    {
        return $this->name;
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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
