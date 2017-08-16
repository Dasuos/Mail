<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class HtmlMessage extends \Tester\TestCase {

	public function testReturningAlternativeContentType() {
		$content = '<h1>Foo</h1><p>Bar</p>';
		Assert::equal(
			sprintf(
				'Content-Type: multipart/alternative; boundary="%s"',
				md5(substr($content, 0, 10))
			), (new Mail\HtmlMessage($content))->headers()
		);
	}

	public function testReturningContentWithLessThanTenCharacters() {
		Assert::exception(
			function() {
				(new Mail\HtmlMessage('foo bar'))->content();
			}, \UnexpectedValueException::class
		);
	}

	public function testReturningContent() {
		$content = '<h1>Foo</h1><p>Bar</p>';
		Assert::same(
			preg_replace('/\s+/', ' ',
				'--df5eb714b4d4a1bc3f705b74fcbdd0ee 
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 

				\nFoo\n\nBar\n 

				--df5eb714b4d4a1bc3f705b74fcbdd0ee 
				Content-Type: text/html; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
				
				<h1>Foo</h1><p>Bar</p> 

				--df5eb714b4d4a1bc3f705b74fcbdd0ee--'
			),
			preg_replace('/\s+/', ' ',
				(new Mail\HtmlMessage($content))->content()
			)
		);
	}


}

(new HtmlMessage())->run();
