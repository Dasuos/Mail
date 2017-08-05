<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MassMail implements Mail {

	public const BCC = 'Bcc';
	public const CC = 'Cc';

	private $origin;
	private $list;
	private $header;

	public function __construct(
		Mail $origin, array $list, string $header = self::BCC
	) {
		$this->origin = $origin;
		$this->list = $list;
		$this->header = $header;
	}

	public function send(
		string $to,
		string $subject,
		Message $message,
		string $headers = self::NO_HEADERS
	): void {
		$headers .= $this->header($this->list);
		$this->origin->send($to, $subject, $message, $headers);
	}

	private function header(array $list): string {
		return sprintf('%s: %s', $this->header, implode(',', $list));
	}
}