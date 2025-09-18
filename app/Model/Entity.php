<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

use LeanMapper\Reflection\Property;
use Ulid\Ulid;

/**
 * @property string $id
 * @property \DateTimeImmutable $createdAt
 * @property \DateTimeImmutable $updatedAt
 * @property-read bool $deleted
 */
class Entity extends \LeanMapper\Entity {
	protected bool $isNew = true;

	public function isNew(): bool {
		return $this->isNew;
	}

	public function setAsNotNew(): void {
		$this->isNew = false;
	}

	protected function decodeRowValue($value, Property $property) {
		if (
			is_string($value) &&
			('id' === $property->getName()) &&
			!(new Ulid())->isValidFormat($value)
		) {
			throw new \LogicException('Invalid value for ULID: ' . $value);
		}

		return parent::decodeRowValue($value, $property);
	}

}