<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

use Ulid\Ulid;

/**
 * @property string $originalUrl
 * @property string $alias
 */
class Signpost extends \WaymarkTo\Model\Entity {
	protected function initDefaults(): void {
		parent::initDefaults();
		$this->alias ??= md5(uniqid('', true)); //@todo
		$this->id ??= (new Ulid())->generate();
	}

}