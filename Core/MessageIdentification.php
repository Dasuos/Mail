<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class MessageIdentification {

	/**
	 * @internal
	 */
	public function __toString(): string {
		return sprintf('<%s.%s@%s>', time(), $this->hash(), $this->domain());
	}

	private function hash(): string {
		return bin2hex(random_bytes(10));
	}

	private function domain(): string {
		return isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: php_uname('n');
	}
}
