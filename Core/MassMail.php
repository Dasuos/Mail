<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class MassMail implements Mail {

	private $origin;
	private $list;
	private $header;

	public function __construct(
		Mail $origin,
		array $list,
		string $header = 'Bcc'
	) {
		$this->origin = $origin;
		$this->list = $list;
		$this->header = $header;
	}

	public function send(
		string $to,
		string $subject,
		Message $message,
		array $headers = self::NO_HEADERS
	): void {
		$this->origin->send(
			$to,
			$subject,
			$message,
			$this->header($this->header, $this->list)
		);
	}

	private function header(string $header, array $list): array {
		if (!in_array($header, ['Bcc', 'Cc']))
			throw new \UnexpectedValueException(
				'Only Bcc anc Cc headers are allowed'
			);
		if (!$list)
			throw new \UnexpectedValueException('Mail list is empty');
		return [$header => implode(',', $list)];
	}
}