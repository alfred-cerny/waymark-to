<?php

declare(strict_types=1);

namespace WaymarkToTesting;

require __DIR__ . '/Bootstrap.php';

use Tester\Assert;

class EnvTest extends \Tester\TestCase {
    public function testContainerAvailability() {
        Assert::true(TestContainer::getContainer() !== null);
    }

    public function testDibiConnectionAvailability() {
        $container = TestContainer::getContainer();
        /** @var Connection $dibiConnection */
        $dibiConnection = $container->getByType(\Dibi\Connection::class);

        Assert::true($dibiConnection !== null);
    }
}

(new EnvTest)->run();