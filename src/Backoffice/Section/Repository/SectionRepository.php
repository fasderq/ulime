<?php
namespace Ulime\Backoffice\Section\Repository;


use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Ulime\Backoffice\Section\Model\Section;

class SectionRepository
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getSection(string $name): Section
    {
        $section = $this->client
            ->selectCollection('ulime', 'section')
            ->find(['name' => $name]);

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

    protected function documentToSection(BSONDocument $document): Section
    {
        return new Section(
            $document->title,
            $document->name
        );
    }
}
