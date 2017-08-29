<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MailHeader {

	private $naming;
	private $value;

	public function __construct(string $naming, string $value) {
		$this->naming = $naming;
		$this->value = $value;
	}

	public function __toString(): string {
		return sprintf('%s: %s', $this->naming, $this->value);
	}
}