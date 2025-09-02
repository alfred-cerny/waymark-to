<?php

declare(strict_types=1);

namespace WaymarkTo\Model\DTO;

use Ulid\Ulid;

readonly class Signpost {
	protected function __construct(
		public ?string             $id,
		public ?string             $originalUrl,
		public ?string             $shortCode = null,
		public ?\DateTimeImmutable $expiresAt = null
	) {}

	public static function new(string $url): self {
		return new self(
			id: (new Ulid())->generate(),
			originalUrl: $url,
			shortCode: substr(md5(random_bytes(3)), 0, 5),
			expiresAt: new \DateTimeImmutable(),
		);
	}

	public static function fromRow(array $row): self {
		$instance = new self(
			id: $row['id'] ?? throw new \InvalidArgumentException('Missing required field: id'),
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

	/**
	 * @throws \JsonException
	 */
	public function __toString(): string {
		return json_encode($this->getValueMap(), JSON_THROW_ON_ERROR);
	}

	public function __serialize(): array {
		return $this->getValueMap();
	}

	public function __toArray(): array {
		return $this->getValueMap();
	}

}