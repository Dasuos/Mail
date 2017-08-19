<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class PlainMessage extends \Tester\TestCase {

	public function testReturningContentType() {
		Assert::same(
			'Content-Type: text/plain; charset=utf-8'
			. PHP_EOL . 'Content-Transfer-Encoding: 7bit',
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
