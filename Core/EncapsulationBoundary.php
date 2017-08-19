<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class EncapsulationBoundary implements Boundary {

	private const EMPTY_INPUT = '';

	private $input;

	public function __construct(string $input = self::EMPTY_INPUT) {
		$this->input = $input;
	}

	public function hash(): string {
		if (!$this->input)
			$this->input = bin2hex(random_bytes(10));
		return md5($this->input);
	}
}