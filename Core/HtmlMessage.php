<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class HtmlMessage implements Message {

	private const EMPTY_BOUNDARY = '';
	private const HTML_REPLACEMENTS = [
		'~<!--.*-->~sU' => '',
		'~<(script|style|head).*</\\1>~isU' => '',
		'~<(td|th|dd)[ >]~isU' => '\\0',
		'~\\s+~u' => ' ',
		'~<(/?p|/?h\\d|li|dt|br|hr|/tr)[ >/]~i' => '\n\\0',
	];

	private $content;
	private $boundary;

	public function __construct(
		string $content, string $boundary = self::EMPTY_BOUNDARY
	) {
		$this->content = $content;
		$this->boundary = $boundary;
	}

	public function headers(): string {
		return sprintf(
			'Content-Type: multipart/alternative; boundary="%s"',
			$this->boundary()
		);
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

	private function text(string $boundary, string $content): string {
		return $this->boundHeaders($boundary, 'plain') .
			strip_tags(
				html_entity_decode(
					array_reduce(
						array_keys(self::HTML_REPLACEMENTS),
						function(string $content, string $pattern): string {
							return preg_replace(
								$pattern,
								self::HTML_REPLACEMENTS[$pattern],
								$content
							);
						}, $content
					), ENT_QUOTES, self::CHARSET
				)
			);
	}

	private function html(string $boundary, string $content): string {
		return $this->boundHeaders($boundary, 'html') . $content;
	}

	private function boundHeaders(string $boundary, string $type): string {
		return implode(PHP_EOL, [
			'--' . $boundary,
			sprintf('Content-Type: text/%s; charset=%s', $type, self::CHARSET),
			'Content-Transfer-Encoding: 7bit'
		]) . PHP_EOL . PHP_EOL;
	}

	private function boundary(): string {
		return (new EncapsulationBoundary($this->boundary))->hash();
	}
}