<?php

declare(strict_types=1);

namespace WaymarkToTesting\Model\Signpost;

require __DIR__ . '/../../../Bootstrap.php';

use Tester\Assert;
use WaymarkToTesting\TestContainer;

class SignpostRepositoryTest extends \Tester\TestCase {
    public function getRepository() {
        $container = TestContainer::getContainer();
        return $container->getByType(\WaymarkTo\Model\Signpost\SignpostRepository::class);
    }

    public function testAvailability() {
        Assert::true($this->getRepository() !== null);
    }

    public function testPersistence() {
        $signpost = new \WaymarkTo\Model\Signpost();
        Assert::true($signpost->isNew());
        $repository = $this->getRepository();

        $repository->persist($signpost);
        Assert::false($signpost->isNew());
    }

}

(new SignpostRepositoryTest)->run();