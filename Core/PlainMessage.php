<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class PlainMessage implements Message {

	private const NO_FOOTER = '';

	private $content;
	private $footer;

	public function __construct(
		string $content, string $footer = self::NO_FOOTER
	) {
		$this->content = $content;
		$this->footer = $footer;
	}

	public function type(): string {
		return 'text/plain; charset=' . self::CHARSET . PHP_EOL .
			'Content-Transfer-Encoding: 7bit';
	}

	public function content(): string {
		return $this->content . ($this->footer ? '-- ' . $this->footer : '');
	}
}