<?php
namespace Ulime\Backoffice\Section\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Backoffice\Category\Model\Category;
use Ulime\Backoffice\Section\Model\Section;

/**
 * Class SectionRepository
 * @package Ulime\Backoffice\Section\Repository
 */
class SectionRepository
{
    protected $client;

    /**
     * SectionRepository constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $name
     * @return Section
     * @throws \Exception
     */
    public function getSectionByName(string $name): Section
    {
        $section = $this->client
            ->selectCollection('ulime', 'section')
            ->findOne(['name' => $name]);

        if (!empty($section)) {
            if ($section instanceof BSONDocument) {
                return $this->documentToSection($section);
            } else {
                throw new \Exception();
            }
        } else {
            throw new \Exception();
        }
    }

    /**
     * @return Section[]
     */
    public function getSections()
    {
        $data = $this->client
            ->selectCollection('ulime', 'section')
            ->find()
            ->toArray();

        $sections = [];
        foreach ($data as $section) {
            $sections[] = $this->documentToSection($section);
        }

        return $sections;
    }

    /**
     * @param Section $section
     */
    public function addSection(Section $section): void
    {
        $this->client
            ->selectCollection('ulime', 'section')
            ->insertOne($this->sectionToRow($section));
    }

    /**
     * @param Section $section
     * @param string $sectionName
     */
    public function editSection(Section $section, string $sectionName): void
    {
        $this->client
            ->selectCollection('ulime', 'section')
            ->findOneAndUpdate(
                ['name' => $sectionName],
                ['$set' => $this->sectionToRow($section)]
            );
    }

    /**
     * @param string $name
     */
    public function deleteSection(string $name): void
    {
        $this->client
            ->selectCollection('ulime', 'section')
            ->deleteOne(['name' => $name]);
    }

    /**
     * @param BSONDocument $document
     * @return Section
     */
    protected function documentToSection(BSONDocument $document): Section
    {
        $categories = [];
        if (!empty($document->categories)) {
            foreach ($document->categories as $category) {
                $categories[] = $this->documentToCategory($category);
            }
        }

        return new Section(
            $document->title,
            $document->name,
            $categories
        );
    }

    /**
     * @param BSONDocument $document
     * @return Category
     */
    protected function documentToCategory(BSONDocument $document): Category
    {
        return new Category(
            $document->title,
            $document->name
        );
    }

    /**
     * @param Section $section
     * @return array
     */
    protected function sectionToRow(Section $section): array
    {
        $categories = [];
        foreach ($section->getCategories() as $category) {
            $categories[] = $this->categoryToRow($category);
        }

        return [
            'title' => $section->getTitle(),
            'name' => $section->getName(),
            'categories' => $categories
        ];
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
}
