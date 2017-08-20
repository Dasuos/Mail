<?php
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class MessageWithAttachment extends \Tester\TestCase {

	public function testReturningMixedContentType() {
		Assert::same(
			['Content-Type' => preg_replace(
				'~"[0-9a-z]*"~',
				'""',
				'multipart/mixed; boundary="81fd830c85363675edb98d2879916d8c"'
			)],
			array_map(
				function($value) {
					return preg_replace('~"[0-9a-z]*"~', '""', $value);
				},
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage('content', ['header' => 'value']),
					__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt'
				))->headers()
			)
		);
	}

	public function testReturningPlainTextWithAttachment() {
		Assert::same(
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
			preg_replace('/\s+/', ' ',
				'--81fd830c85363675edb98d2879916d8c
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
			
				content 
			
				--81fd830c85363675edb98d2879916d8c 
				Content-Type: application/octet-stream; name="attachment.txt" 
				Content-Transfer-Encoding: base64 
				Content-Disposition: attachment; filename="attachment.txt" 
			
				dGVzdGluZyBjb250ZW50 
			
				--81fd830c85363675edb98d2879916d8c--'
			)),
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
			preg_replace('/\s+/', ' ',
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage(
						'content',
						[
							'Content-Type' => 'text/plain; charset=utf-8',
							'Content-Transfer-Encoding' => '7bit'
						]
					),
					__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt'
				))->content()
			))
		);
	}

	public function testThrowingNonexistentAttachmentException() {
		Assert::exception(
			function() {
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage(
						'content',
						['header' => 'value']
					), 'nonexistent/path'
				))->content();
			},
			\UnexpectedValueException::class,
			'Attached file does not exist'
		);
	}
}

(new MessageWithAttachment())->run();