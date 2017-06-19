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
    protected $img;

    /**
     * Article constructor.
     * @param string $name
     * @param string $title
     * @param string $label
     * @param string $body
     * @param string $img
     */
    public function __construct(
        string $name,
        string $title,
        string $label,
        string $body,
        ?string $img = null
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->label = $label;
        $this->body = $body;
        $this->img = $img;
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

    /**
     * @return null|string
     */
    public function getImage(): ?string
    {
        return $this->img;
    }
}
