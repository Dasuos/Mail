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
		$headers .= $this->header($this->header, $this->list);
		$this->origin->send($to, $subject, $message, $headers);
	}

	private function header(string $header, array $list): string {
		if (!in_array($header, [self::BCC, self::CC]))
			throw new \UnexpectedValueException(
				'Only Bcc anc Cc headers are allowed'
			);
		return sprintf('%s: %s', $header, implode(',', $list));
	}
}