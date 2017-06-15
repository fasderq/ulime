<?php
namespace Ulime\Frontoffice\General\Model;

/**
 * Class Article
 * @package Ulime\Frontoffice\General\Model
 */
class Article
{
    protected $title;
    protected $label;
    protected $body;
    protected $views;

    /**
     * Article constructor.
     * @param string $title
     * @param string $label
     * @param string $body
     * @param int|null $views
     */
    public function __construct(
        string $title,
        string $label,
        string $body,
        ?int $views = null
    ) {
        $this->title = $title;
        $this->label = $label;
        $this->body = $body;
        $this->views = $views;
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
     * @return int|null
     */
    public function getViews(): ?int
    {
        return $this->views;
    }
}
