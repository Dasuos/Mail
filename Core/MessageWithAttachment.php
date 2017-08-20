<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MessageWithAttachment implements Message {

	private $origin;
	private $path;
	private $boundary;

	public function __construct(Message $origin, string $path) {
		$this->origin = $origin;
		$this->path = $path;
	}

	public function headers(): array {
		return [
			'Content-Type' => sprintf(
				'multipart/mixed; boundary="%s"', $this->boundary()
			)
		];
	}

	public function content(): string {
		$boundary = $this->boundary();
		return implode(PHP_EOL . PHP_EOL, [
			'--' . $boundary . PHP_EOL .
			implode(
				PHP_EOL, array_map(
					function ($value, $header) {
						return sprintf('%s: %s', $header, $value);
					},
					$this->origin->headers(),
					array_keys($this->origin->headers())
				)
			),
			$this->origin->content(),
			$this->attachment($boundary, $this->path)
		]);
	}

	private function attachment(string $boundary, string $path): string {
		$name = basename($path);
		return implode(PHP_EOL, [
			'--' . $boundary,
			sprintf('Content-Type: application/octet-stream; name="%s"', $name),
			'Content-Transfer-Encoding: base64',
			sprintf('Content-Disposition: attachment; filename="%s"', $name),
			PHP_EOL . $this->file($path),
			'--' . $boundary . '--',
		]);
	}

	private function existingPath(string $path): string {
		if (!file_exists($path))
			throw new \UnexpectedValueException('Attached file does not exist');
		return $path;
	}

	private function file(string $path): string {
		return chunk_split(base64_encode(
			file_get_contents($this->existingPath($path))
		));
	}

	private function boundary(): string {
		if (!$this->boundary)
			$this->boundary = bin2hex(random_bytes(10));
		return md5($this->boundary);
	}
}