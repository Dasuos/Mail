<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

function mail(
	string $to,
	string $subject,
	string $message,
	string $headers = '',
	string $parameters = ''
) {
	echo $headers;
	return true;
}

use Dasuos\Mail\Misc\ExemplaryHeaders;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */

final class MassMailTest extends \Tester\TestCase {

	public function testReturningBccHeaderWithHtmlContentType() {
		ob_start();
		(new MassMail(
			new AssembledMail('from@bar.cz', AssembledMail::HIGH_PRIORITY),
			['foo@bar.cz', 'bar@foo.cz']
		))->send(
			'foobar@bar.cz',
			'foo',
			new HtmlMessage('<h1>foo</h1><p>bar</p>')
		);
		$result = ob_get_clean();
		Assert::contains(
			preg_replace(
				'~\s+~',
				' ',
				'MIME-Version: 1.0
				From: from@bar.cz
				Return-Path: from@bar.cz
				Date: example
				X-Sender: from@bar.cz
				X-Mailer: PHP/example
				X-Priority: 1
				Message-Id: example
				Content-Type: multipart/alternative; boundary="example"
				Bcc: foo@bar.cz,bar@foo.cz'
			),
			(string) new ExemplaryHeaders($result)
		);
	}

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
