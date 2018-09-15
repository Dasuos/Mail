<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class RandomBoundary implements Boundary {

	private const LENGTH = 10;

	/**
	 * @internal
	 */
	public function hash(): string {
		return bin2hex(random_bytes(self::LENGTH));
	}
}