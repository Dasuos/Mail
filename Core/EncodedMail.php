<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class EncodedMail implements Mail {

	private $origin;
	private $encoding;

	public function __construct(Mail $origin, string $encoding = 'utf-8') {
		$this->origin = $origin;
		$this->encoding = $encoding;
	}

	public function send(
		string $to, string $subject, string $message, string $headers = ''
	): void {
		$this->origin->send(
			$to, $this->subject($subject), $message, $this->headers($headers)
		);
	}

	private function subject(string $content): string {
		iconv_set_encoding('internal_encoding', $this->encoding);
		return substr(
			iconv_mime_encode('Subject', $content),
			strlen('Subject: ')
		);
	}

	private function headers(string $additional = ''): string {
		$headers = implode(
			PHP_EOL, [
				'MIME-Version: 1.0',
				'Content-Type: text/plain: charset=' . $this->encoding,
				'Content-Transfer-Encoding: 8bit',
			]
		);
		if ($additional)
			$headers .= PHP_EOL . $additional;
		return $headers;
	}
}