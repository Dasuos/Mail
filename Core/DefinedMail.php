<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class DefinedMail implements Mail {

	private const CHARSET = 'utf-8';
	private const NO_HEADERS = '';

	private $origin;
	private $message;
	private $from;

	public function __construct(Mail $origin, Message $message, string $from) {
		$this->origin = $origin;
		$this->message = $message;
		$this->from = $from;
	}

	public function send(
		string $to,
		string $subject,
		string $message,
		string $headers = self::NO_HEADERS
	): void {
		$this->origin->send(
			$to,
			$this->subject($subject),
			$this->message->content(),
			$this->headers($this->from, $this->message, $headers)
		);
	}

	private function subject(string $content): string {
		iconv_set_encoding('internal_encoding', self::CHARSET);
		return substr(
			iconv_mime_encode('Subject', $content),
			strlen('Subject: ')
		);
	}

	private function headers(
		string $from, Message $message, string $additional = self::NO_HEADERS
	): string {
		$headers = implode(
			PHP_EOL, [
				'MIME-Version: 1.0',
				'From: ' . $from,
				'Return-Path: ' . $from,
				'Date: ' . date('r'),
				'X-Sender: ' . $from,
				'X-Mailer: PHP/' . phpversion(),
				'X-Priority: 1',
				$message->type(),
			]
		);
		if ($additional)
			$headers .= PHP_EOL . $additional;
		return $headers;
	}
}