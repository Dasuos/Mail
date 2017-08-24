<?php
declare(strict_types = 1);
namespace Dasuos\Tests\TestCase;

final class SequentialBoundaries {

	private $boundaries;
	private $sequence;

	public function __construct(array $boundaries, array $sequence) {
		$this->boundaries = $boundaries;
		$this->sequence = $sequence;
	}

	public function identical(): bool {
		return count(array_unique(array_filter(
			$this->boundaries,
			function($key) {
				return !in_array($key, $this->sequence);
			},
			ARRAY_FILTER_USE_KEY
		))) === 1;
	}
}