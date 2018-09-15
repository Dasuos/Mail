<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class PlainMessage implements Message {

	private const NO_FOOTER = '';

	private $content;
	private $footer;

	public function __construct(
		string $content,
		string $footer = self::NO_FOOTER
	) {
		$this->content = $content;
		$this->footer = $footer;
	}

	public function headers(): array {
		return [
			'Content-Type' => sprintf('text/plain; charset=%s', self::CHARSET),
			'Content-Transfer-Encoding' => '7bit',
		];
	}

	public function content(): string {
		return $this->content . ($this->footer ? '-- ' . $this->footer : '');
	}
}