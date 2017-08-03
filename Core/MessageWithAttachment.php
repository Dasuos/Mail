<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MessageWithAttachment implements Message {

	private $origin;
	private $boundary;
	private $path;

	public function __construct(
		Message $origin, string $boundary, string $path
	) {
		$this->origin = $origin;
		$this->boundary = $boundary;
		$this->path = $path;
	}

	public function type(): string {
		return $this->origin->type() . PHP_EOL . sprintf(
			'Content-Type: multipart/mixed; boundary="%s"', $this->boundary
		);
	}

	public function content(): string {
		return $this->origin->content() . $this->attachment($this->path);
	}

	private function attachment(string $path): string {
		$file =  $this->existingPath($path);
		$name = basename($file);
		return implode(PHP_EOL, [
			PHP_EOL . $this->boundary(),
			$this->binaryType($name),
			'Content-Transfer-Encoding: base64',
			$this->disposition($name) . PHP_EOL,
			$this->file($file),
			$this->boundary() . '--',
		]);
	}

	private function existingPath(string $path): string {
		if (!file_exists($path))
			throw new \UnexpectedValueException('Attached file does not exist');
		return $path;
	}

	private function boundary() {
		return PHP_EOL . '--' . $this->boundary;
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
}