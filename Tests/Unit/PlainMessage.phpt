<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Mail\Unit;

use Dasuos\Mail;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class PlainMessage extends \Tester\TestCase {

	public function testReturningContentType() {
		Assert::same(
			[
				'Content-Type' => 'text/plain; charset=utf-8',
				'Content-Transfer-Encoding' => '7bit',
			],
			(new Mail\PlainMessage('foo bar'))->headers()
		);
	}

	public function testReturningPlainText() {
		Assert::same(
			'foo bar',
			(new Mail\PlainMessage('foo bar'))->content()
		);
	}

	public function testReturningPlainTextWithFooter() {
		Assert::same(
			'foo bar-- signature',
			(new Mail\PlainMessage('foo bar', 'signature'))->content()
		);
	}
}

(new PlainMessage())->run();
