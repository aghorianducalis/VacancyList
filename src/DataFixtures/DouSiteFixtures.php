<?php

namespace App\DataFixtures;

use App\Entity\Parser;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DouSiteFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $site = new Site();
        $site->setName('DOU');
        $site->setDomain('https://jobs.dou.ua/');
        $site->setSlug('dou');

        /**
         * This reference returns the Parser object created in DouParserFixtures.
         * @var Parser $parser
         */
        $parser = $this->getReference(DouParserFixtures::PARSER_REFERENCE);

        $site->setParser($parser);

        $manager->persist($site);
        $manager->flush();
    }

    /**
     * This returns an array of the fixture classes that must be loaded before this one.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            DouParserFixtures::class,
        ];
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
