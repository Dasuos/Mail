<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MassMail implements Mail {

	private const NO_HEADERS = '';
	private const INVISIBLE = false;

	private $origin;
	private $list;
	private $visible;

	public function __construct(
		Mail $origin, array $list, bool $visible = self::INVISIBLE
	) {
		$this->origin = $origin;
		$this->list = $list;
		$this->visible = $visible;
	}

	public function send(
		string $to,
		string $subject,
		Message $message,
		string $headers = self::NO_HEADERS
	): void {
		$headers .= $this->visible ?
			$this->header('Bcc', $this->list) :
			$this->header('Cc', $this->list);
		$this->origin->send($to, $subject, $message, $headers);
	}

	private function header(string $name, array $list): string {
		return sprintf('%s: %s', $name, implode(',', $list));
	}
}