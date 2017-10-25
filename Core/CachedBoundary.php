<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class CachedBoundary implements Boundary {

	private $origin;
	private $hash;

	public function __construct(Boundary $origin) {
		$this->origin = $origin;
	}

	/**
	 * @internal
	 */
	public function hash(): string {
		if ($this->hash === null)
			$this->hash = $this->origin->hash();
		return $this->hash;
	}
}