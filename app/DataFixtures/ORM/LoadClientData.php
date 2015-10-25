<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Client\Client;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en');

        for ($i = 1; $i <= 100; $i++) {
            $name = $faker->name;

            $phones = [];
            $countPhones = rand(0, 5);

            for ($j = 0; $j < $countPhones; $j++) {
                $phones[] = $faker->phoneNumber;
            }

            $emails = [];
            $countEmails = rand(0, 5);

            for ($j = 0; $j < $countEmails; $j++) {
                $emails[] = $faker->email;
            }

            $client = new Client($name);
            $client
                ->setCountry($faker->countryCode)
                ->setCity($faker->city)
                ->setAddress($faker->address)
                ->setPhones($phones)
                ->setEmails($emails)
                ->setNotes($faker->text);

            $manager->persist($client);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
