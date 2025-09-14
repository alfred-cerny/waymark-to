<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

use LeanMapper\Reflection\Property;
use Ulid\Ulid;

class Entity extends \LeanMapper\Entity {
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