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
	private const DEFAULT_BOUNDARY_SEED = 'default_seed';

	private $content;

	public function __construct(string $content) {
		$this->content = $content;
	}

	public function type(): string {
		return 'Content-Type: multipart/alternative; boundary=' .
			$this->boundary();
	}

	public function content(): string {
		$boundary = $this->boundary();
		return implode(
			PHP_EOL . PHP_EOL, [
				$this->text($boundary, $this->content),
				$this->html($boundary, $this->content),
				'--' . $boundary . '--'
			]
		);
	}

	private function html(string $boundary, string $content): string {
		return $this->headers($boundary, 'html') . $content;
	}

	private function text(string $boundary, string $content): string {
		$text = array_reduce(
			array_keys(self::HTML_REPLACEMENTS),
			function(string $content, string $pattern): string {
				return preg_replace(
					$pattern, self::HTML_REPLACEMENTS[$pattern], $content
				);
			}, $content
		);
		return $this->headers($boundary, 'plain') . strip_tags(
				html_entity_decode($text, ENT_QUOTES, self::CHARSET)
			);
	}

	private function headers(string $boundary, string $type): string {
		return '--' . $boundary . $this->contentType($type) .
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

	private function boundary(): string {
		if (strlen($this->content) >= 10)
			return md5(substr($this->content(), 0, 10));
		return md5(self::DEFAULT_BOUNDARY_SEED);
	}
}