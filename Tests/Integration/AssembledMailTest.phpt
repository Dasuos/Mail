<?php
/**
 * @testCase
 * @phpVersion > 7.1
 */

namespace Dasuos\Mail;

function mail(
	string $to,
	string $subject,
	string $message,
	string $headers = '',
	string $parameters = ''
) {
	printf(
		"To: %s \n Subject: %s \n Message: %s \n Headers: %s \n Parameters: %s",
		$to,
		$subject,
		$message,
		$headers,
		$parameters
	);
	return true;
}

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class AssembledMailTest extends \Tester\TestCase {

	public function testReturningToHeader() {
		ob_start();
		(new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY))->send(
			'foo@bar.cz',
			'foo',
			new FakeMessage('message', [])
		);
		$result = ob_get_clean();
		Assert::contains('To: foo@bar.cz', $result);
	}

	public function testReturningSubjectHeaderWithDiacritics() {
		ob_start();
		(new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY))->send(
			'foo@bar.cz',
			'Předmět',
			new FakeMessage('message', [])
		);
		$result = ob_get_clean();
		Assert::contains('Subject: =?UTF-8?B?UMWZZWRtxJt0?=', $result);
	}

	public function testReturningHeadersWithHtmlContentType() {
		ob_start();
		(new AssembledMail('from@bar.cz', AssembledMail::HIGH_PRIORITY))->send(
			'to@bar.cz',
			'foo',
			new HtmlMessage('<h1>foo</h1><p>bar</p>')
		);
		$result = ob_get_clean();
		Assert::contains(
			preg_replace('~\s+~', ' ',
				'Headers: 
				MIME-Version: 1.0
				From: from@bar.cz
				Return-Path: from@bar.cz
				Date: actual
				X-Sender: from@bar.cz
				X-Mailer: PHP/version
				X-Priority: 1
				Content-Type: multipart/alternative; boundary="random"'
			),
			preg_replace('~[0-9a-z]{20}~', 'random',
				preg_replace('~\s+~', ' ',
					preg_replace('~X-Mailer: PHP/\d\.\d\.\d~',
						'X-Mailer: PHP/version',
						preg_replace(
							'~Date: \w+,\s\d{2}\s\w+\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}~',
							'Date: actual',
							$result
						)
					)
				)
			)
		);
	}
}

(new AssembledMailTest())->run();