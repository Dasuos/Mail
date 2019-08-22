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
		$this->boundary = new RandomBoundary;
	}

	public function headers(): array {
		return [
			'Content-Type' => sprintf(
				'multipart/mixed; boundary="%s"',
				$this->boundary->hash()
			),
		];
	}

	public function content(): string {
		return implode(
			PHP_EOL . PHP_EOL,
			[
				$this->boundary->begin() . PHP_EOL . new Headers($this->origin->headers()),
				$this->origin->content(),
				$this->attachment($this->boundary, $this->path),
			]
		);
	}

	private function attachment(
		RandomBoundary $boundary,
		string $path
	): string {
		$name = basename($path);
		return implode(PHP_EOL, [
			$boundary->begin(),
			new Headers([
				'Content-Type' => sprintf(
					'application/octet-stream; name="%s"',
					$name
				),
				'Content-Transfer-Encoding' => 'base64',
				'Content-Disposition' => sprintf(
					'attachment; filename="%s"',
					$name
				),
			]),
			PHP_EOL . $this->file($path),
			$boundary->end(),
		]);
	}

	private function file(string $path): string {
		if (file_exists($path))
			return chunk_split(base64_encode(file_get_contents($path)));
		throw new \UnexpectedValueException('Attached file does not exist');
	}
}
