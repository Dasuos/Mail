<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MailWithAttachment implements Mail {

	private const NO_HEADERS = '';

	private $origin;
	private $boundary;
	private $path;

	public function __construct(Mail $origin, string $boundary, string $path) {
		$this->origin = $origin;
		$this->boundary = $boundary;
		$this->path = $path;
	}

	public function send(
		string $to,
		string $subject,
		string $message,
		string $headers = self::NO_HEADERS
	): void {
		$message .= $this->attachment($this->path);
		$this->origin->send(
			$to, $subject, $message, $headers = $this->headers()
		);
	}

	private function headers(): string {
		return sprintf(
			'Content-Type: multipart/mixed; boundary="%s"', $this->boundary
		);
	}

	private function attachment(string $path): string {
		$file =  $this->existingPath($path);
		$name = basename($file);
		return implode(PHP_EOL, [
			'--' . $this->boundary,
			$this->contentType($name),
			'Content-Transfer-Encoding: base64',
			$this->contentDisposition($name),
			$this->fileContent($file),
			PHP_EOL . '--' . $this->boundary . '--',
		]);
	}

	private function existingPath(string $path): string {
		if (!file_exists($path))
			throw new \UnexpectedValueException('Attached file does not exist');
		return $path;
	}

	private function contentType(string $name): string {
		return sprintf(
			'Content-Type: application/octet-stream; name="%s"', $name
		);
	}

	private function contentDisposition($name): string {
		return sprintf(
			'Content-Disposition: attachment; filename="%s"%s', $name, PHP_EOL
		);
	}

	private function fileContent(string $path): string {
		return chunk_split(base64_encode(file_get_contents($path)));
	}
}