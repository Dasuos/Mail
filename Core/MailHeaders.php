<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MailHeaders implements Headers {

	private $list;

	public function __construct(array $list) {
		$this->list = $list;
	}

	public function list(): array {
		return array_map(
			function (string $value, string $header): string {
				return sprintf('%s: %s', $header, $value);
			},
			$this->list,
			array_keys($this->list)
		);
	}
}