<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class RandomBoundary implements Boundary {

	private $hash;

	/**
	 * @internal
	 */
	public function hash(): string {
		if (!$this->hash)
			$this->hash = bin2hex(random_bytes(10));
		return $this->hash;
	}

	/**
	 * @internal
	 */
	public function begin(): string {
		return '--' . $this->hash();
	}

	/**
	 * @internal
	 */
	public function end(): string {
		return sprintf('--%s--', $this->hash());
	}
}
