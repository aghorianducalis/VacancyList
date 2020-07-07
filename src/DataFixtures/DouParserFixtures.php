<?php

namespace App\DataFixtures;

use App\Entity\Parser;
use App\Service\DouParser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DouParserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Other fixtures can get the parser object using the reference constant, i. e. DouParserFixtures::PARSER_REFERENCE
     */
    public const PARSER_REFERENCE = 'dou';

    public function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $parser->setClass(DouParser::class);

        $manager->persist($parser);
        $manager->flush();

        $this->addReference(self::PARSER_REFERENCE, $parser);
    }

    /**
     * Pass the --group option to load only the fixtures associated with those groups.
     * php bin/console doctrine:fixtures:load --group=dou
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return ['dou'];
    }
}
