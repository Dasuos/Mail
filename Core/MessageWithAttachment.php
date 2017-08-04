<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MessageWithAttachment implements Message {

	private $origin;
	private $path;

	public function __construct(
		Message $origin, string $path
	) {
		$this->origin = $origin;
		$this->path = $path;
	}

	public function type(): string {
		return $this->origin->type() . PHP_EOL . sprintf(
			'Content-Type: multipart/mixed; boundary="%s"',
			$this->boundary($this->path)
		);
	}

	public function content(): string {
		$path = $this->existingPath($this->path);
		return $this->origin->content() . PHP_EOL . PHP_EOL .
			$this->attachment($this->boundary($path), $path);
	}

	private function attachment(string $boundary, string $path): string {
		$name = basename($path);
		return implode(PHP_EOL, [
			'--' . $boundary,
			$this->binaryType($name),
			'Content-Transfer-Encoding: base64',
			$this->disposition($name) . PHP_EOL,
			$this->file($path),
			PHP_EOL . '--' . $boundary . '--',
		]);
	}

	private function existingPath(string $path): string {
		if (!file_exists($path))
			throw new \UnexpectedValueException('Attached file does not exist');
		return $path;
	}

	private function binaryType(string $name): string {
		return sprintf(
			'Content-Type: application/octet-stream; name="%s"', $name
		);
	}

	private function disposition($name): string {
		return sprintf(
			'Content-Disposition: attachment; filename="%s"', $name
		);
	}

	private function file(string $path): string {
		return chunk_split(base64_encode(file_get_contents($path)));
	}

	private function boundary(string $value): string {
		return md5($value);
	}
}