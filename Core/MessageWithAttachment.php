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
		$this->boundary = new CachedBoundary(new RandomBoundary());
	}

	public function headers(): array {
		return [
			'Content-Type' => sprintf(
				'multipart/mixed; boundary="%s"', $this->boundary->hash()
			)
		];
	}

	public function content(): string {
		$boundary = $this->boundary->hash();
		return implode(PHP_EOL . PHP_EOL, [
			'--' . $boundary . PHP_EOL .
			implode(
				PHP_EOL,
				(new MailHeaders($this->origin->headers()))->list()
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
}