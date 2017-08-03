<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class PlainMessage implements Message {

	private const CHARSET = 'utf-8';
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
		return 'Content-Type: text/plain; charset=' . self::CHARSET;
	}

	public function content(): string {
		return $this->content . ($this->footer ? '-- ' . $this->footer : '');
	}
}