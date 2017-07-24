<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class EncodedMail implements Mail {

	private const CHARSET = 'utf-8';
	private const NO_HEADERS = '';

	private $origin;

	public function __construct(Mail $origin) {
		$this->origin = $origin;
	}

	public function send(
		string $to,
		string $subject,
		string $message,
		string $headers = self::NO_HEADERS
	): void {
		$this->origin->send(
			$to, $this->subject($subject), $message, $this->headers($headers)
		);
	}

	private function subject(string $content): string {
		iconv_set_encoding('internal_encoding', self::CHARSET);
		return substr(
			iconv_mime_encode('Subject', $content),
			strlen('Subject: ')
		);
	}

	private function headers(string $additional = self::NO_HEADERS): string {
		$headers = implode(
			PHP_EOL, [
				'MIME-Version: 1.0',
				'Content-Type: text/plain: charset=' . self::CHARSET,
				'Content-Transfer-Encoding: 8bit',
			]
		);
		if ($additional)
			$headers .= PHP_EOL . $additional;
		return $headers;
	}
}