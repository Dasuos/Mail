<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MailHeaders {

	private $list;

	public function __construct(array $list) {
		$this->list = $list;
	}

	public function __toString(): string {
		return implode(
			PHP_EOL,
			array_map(
				function (string $value, string $naming): string {
					return sprintf('%s: %s', $naming, $value);
				},
				$this->list,
				array_keys($this->list)
			)
		);
	}
}