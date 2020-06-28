<?php


namespace App\Service;


use SimpleXMLElement;
use Symfony\Component\DomCrawler\Crawler;

class DouParser implements ParserInterface
{
    public function parseItemList(string $url): array
    {
        $result = [];

        /** @var SimpleXMLElement $rawData */
        $rawData = simplexml_load_file($url);
        $rawData = $rawData->url ?? [];

        foreach ($rawData as $node) {
            if (isset($node->loc)) {
                $result[] = $node->loc->__toString();
            }
        }

        $result = array_unique($result);

        return $result;
    }

    public function parseItem(string $url): array
    {
        $result = [];

        $parseData = file_get_contents($url);

        $crawler = new Crawler($parseData);

        $vacancyNode = $crawler->filter('.b-vacancy')->first();

        $date = $vacancyNode->filter('.date')->first()->text();
        $title = $vacancyNode->filter('h1.g-h2')->first()->text();
        $location = $vacancyNode->filter('.place')->first()->text();

        $descriptionNode = $crawler->filter('.l-vacancy')->first();

        $descriptionNode->filter('.likely')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });
        $descriptionNode->filter('.reply')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // todo trim string
        $description = $descriptionNode->html();

        return compact('title', 'date', 'location', 'description');
    }
}