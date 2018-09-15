<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class FakeMessage implements Message {

	private $content;
	private $headers;

	public function __construct(string $content, array $headers) {
		$this->content = $content;
		$this->headers = $headers;
	}

	public function headers(): array {
		return $this->headers;
	}

	public function content(): string {
		return $this->content;
	}
}