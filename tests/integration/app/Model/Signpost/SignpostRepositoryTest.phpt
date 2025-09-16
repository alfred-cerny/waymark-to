<?php

declare(strict_types=1);

namespace WaymarkToTesting\Model\Signpost;

require __DIR__ . '/../../../Bootstrap.php';

use Tester\Assert;

class SignpostRepositoryTest extends \Tester\TestCase {
    public function testCreation() {
        Assert::true(true); //@todo
    }
}

(new SignpostRepositoryTest)->run();