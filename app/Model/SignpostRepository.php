<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

use Random\RandomException;
use Ulid\Ulid;

class SignpostRepository extends Repository {

	public function persist(Signpost|\LeanMapper\Entity $entity): void {
		if ($entity->isNew()) {
			$entity->alias ??= md5((string)random_int(0, 3)); //@todo
		}

		parent::persist($entity);
		$entity->setAsNotNew();
	}

}