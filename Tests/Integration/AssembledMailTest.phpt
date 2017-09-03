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

	public function testReturning() {
		ob_start();
		(new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY))->send(
			'to@test.cz',
			'subject',
			new FakeMessage('msg', []),
			[]
		);
		$result = ob_get_clean();
		Assert::contains("To: to@test.cz", $result);
	}
}

(new AssembledMailTest())->run();