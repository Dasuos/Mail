<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MessageWithAttachment implements Message {

	private const EMPTY_BOUNDARY = '';

	private $origin;
	private $path;
	private $boundary;

	public function __construct(
		Message $origin,
		string $path,
		string $boundary = self::EMPTY_BOUNDARY
	) {
		$this->origin = $origin;
		$this->path = $path;
		$this->boundary = $boundary;
	}

	public function headers(): string {
		return sprintf(
			'Content-Type: multipart/mixed; boundary="%s"',
			$this->boundary()
		);
	}

	public function content(): string {
		$boundary = $this->boundary();
		return implode(PHP_EOL . PHP_EOL, [
			'--' . $boundary . PHP_EOL . $this->origin->headers(),
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
		return (new EncapsulationBoundary($this->boundary))->hash();
	}
}