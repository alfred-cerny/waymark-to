<?php

declare(strict_types=1);

namespace WaymarkTo\Model\DTO;

use Ulid\Ulid;

readonly class Signpost {
	public function __construct(
		protected ?string $id,
		protected ?string $shortCode,
		protected ?string $originalUrl
	) {}

	public static function new(string $url): self {
		return new self(
			id: (new Ulid())->generate(),
			shortCode: substr(md5(random_bytes(3)), 0, 5),
			originalUrl: $url,
		);
	}

	public static function fromRow(array $row): self {
		$instance = new self(
			id: $row['id'] ?? throw new \LogicException('ID')
		);
		//@todo
		return $instance;
	}

	public function getValueMap(): array {
		return [
			'id' => $this->id,
			'short_code' => $this->shortCode,
			'original_url' => $this->originalUrl,
		];
	}

	public function __toArray(): array {
		return $this->getValueMap();
	}

}