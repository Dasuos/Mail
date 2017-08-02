<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class HtmlMessage implements Message {

	private const CHARSET = 'utf-8';
	private const HTML_REPLACEMENTS = [
		'~<!--.*-->~sU' => '',
		'~<(script|style|head).*</\\1>~isU' => '',
		'~<(td|th|dd)[ >]~isU' => '\\0',
		'~\\s+~u' => ' ',
		'~<(/?p|/?h\\d|li|dt|br|hr|/tr)[ >/]~i' => '\n\\0',
	];

	private $content;
	private $boundary;

	public function __construct(string $content, string $boundary) {
		$this->content = $content;
		$this->boundary = $boundary;
	}

	public function type(): string {
		return 'Content-Type: multipart/alternative; boundary=' .
			$this->boundary;
	}

	public function content(): string {
		return implode(
			PHP_EOL . PHP_EOL, [
				$this->text($this->content),
				$this->html($this->content),
				'--' . $this->boundary . '--'
			]
		);
	}

	private function html(string $content): string {
		return $this->headers('html') . $content;
	}

	private function text(string $content): string {
		$text = array_reduce(
			array_keys(self::HTML_REPLACEMENTS),
			function(string $content, string $pattern): string {
				return preg_replace(
					$pattern, self::HTML_REPLACEMENTS[$pattern], $content
				);
			}, $content
		);
		return $this->headers('plain') . strip_tags(
				html_entity_decode($text, ENT_QUOTES, self::CHARSET)
			);
	}

	private function headers(string $type): string {
		return '--' . $this->boundary . $this->contentType($type) .
			$this->contentTransferEncoding();
	}

	private function contentType(string $type): string {
		return PHP_EOL . sprintf(
			'Content-Type: text/%s; charset=%s', $type, self::CHARSET
		);
	}

	private function contentTransferEncoding(): string {
		return PHP_EOL . 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
	}
}