<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

class SignpostRepository extends Repository {

	public function persist(Signpost|\LeanMapper\Entity $entity): void {
		parent::persist($entity);
		$entity->setAsNotNew();
	}

}