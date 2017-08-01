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
	private $footer;

	public function __construct(
		string $content, string $boundary, string $footer = ''
	) {
		$this->content = $content;
		$this->boundary = $boundary;
		$this->footer = $footer;
	}

	public function type(): string {
		return 'Content-Type: multipart/alternative; boundary=' .
			$this->boundary;
	}

	public function content(): string {
		return $this->text($this->content) . $this->html($this->content) .
			$this->boundary() . '--';
	}

	private function html(string $content): string {
		return $this->boundary() . $this->contentType('html') .
			$this->contentTransferEncoding() . $content . $this->footer();
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
		return $this->boundary() . $this->contentType('html') .
			$this->contentTransferEncoding() . strip_tags(
				html_entity_decode($text, ENT_QUOTES, self::CHARSET)
			) . $this->footer();
	}

	private function boundary(): string {
		return PHP_EOL . PHP_EOL . '--' . $this->boundary;
	}

	private function contentType(string $type): string {
		return PHP_EOL . sprintf(
			'Content-Type: text/%s; charset=%s', $type, self::CHARSET
		);
	}

	private function contentTransferEncoding(): string {
		return PHP_EOL . 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
	}

	private function footer():string {
		return $this->footer ? '-- ' . $this->footer : '';
	}
}