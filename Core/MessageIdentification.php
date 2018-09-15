<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class MessageIdentification {

	private const LENGTH = 10;

	/**
	 * @internal
	 */
	public function __toString(): string {
		return sprintf('<%s.%s@%s>', time(), $this->hash(), $this->domain());
	}

	private function hash(): string {
		return bin2hex(random_bytes(self::LENGTH));
	}

	private function domain(): string {
		return isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: php_uname('n');
	}
}