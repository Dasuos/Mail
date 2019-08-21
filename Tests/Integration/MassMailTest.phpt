<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */

final class MassMailTest extends \Tester\TestCase {

	public function testThrowingOnInvalidHeaders() {
		Assert::exception(
			function() {
				(new MassMail(
					new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY),
					['bar@foo.cz', 'barfoo@foobar.cz'],
					'invalid'
				))->send(
					'foo@bar.cz',
					'foo',
					new FakeMessage('message', [])
				);
			},
			\UnexpectedValueException::class,
			'Only Bcc anc Cc headers are allowed'
		);
	}

	public function testThrowingOnEmptyEmailList() {
		Assert::exception(
			function() {
				(new MassMail(
					new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY),
					[],
					'Bcc'
				))->send(
					'foo@bar.cz',
					'foo',
					new FakeMessage('message', [])
				);
			},
			\UnexpectedValueException::class,
			'Invalid email list'
		);
	}

	public function testThrowingOnInvalidEmailList() {
		Assert::exception(
			function() {
				(new MassMail(
					new AssembledMail('from@test.cz', AssembledMail::HIGH_PRIORITY),
					[
						'foobar@foo.cz',
						'barfoo@bar.cz',
						'invalid',
					],
					'Bcc'
				))->send(
					'foo@bar.cz',
					'foo',
					new FakeMessage('message', [])
				);
			},
			\UnexpectedValueException::class,
			'Invalid email list'
		);
	}
}

(new MassMailTest())->run();
