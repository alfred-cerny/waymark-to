<?php

declare(strict_types=1);

namespace App\Model\Signpost;

use Nette;

class SignpostRepository {
	public function __construct(
		private Nette\Database\Explorer $database,
	) {}

}