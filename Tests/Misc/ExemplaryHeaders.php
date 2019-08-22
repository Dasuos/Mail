<?php
declare(strict_types = 1);

namespace Dasuos\Mail\Misc;

final class ExemplaryHeaders {

	private const REPLACEMENTS = [
		'~Message-Id: <\d+\.[0-9a-z]+@.+>~' => 'Message-Id: example',
		'~Date: \w+, \d{2} \w+ \d{4} \d{2}:\d{2}:\d{2} \+\d{4}~' => 'Date: example',
		'~X-Mailer: PHP/(\d|\.)+~' => 'X-Mailer: PHP/example',
		'~[0-9a-z]{20}~' => 'example',
		'~\s+~' => ' ',
	];

	private $headers;

	public function __construct(string $headers) {
		$this->headers = $headers;
	}

	public function __toString(): string {
		return array_reduce(
			array_keys(self::REPLACEMENTS),
			function(string $content, string $pattern): string {
				return preg_replace(
					$pattern,
					self::REPLACEMENTS[$pattern],
					$content
				);
			},
			$this->headers
		);
	}
}
