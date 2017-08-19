<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class MessageWithAttachment extends \Tester\TestCase {

	public function testReturningMixedContentType() {
		$path = __DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt';
		Assert::same(
			'Content-Type: multipart/mixed; boundary="81fd830c85363675edb98d2879916d8c"',
			(new Mail\MessageWithAttachment(
				new Mail\FakeMessage('content', 'headers'),
				$path, 'boundary'
			))->headers()
		);
	}

	public function testReturningPlainTextWithAttachment() {
		Assert::same(
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
			),
			preg_replace('/\s+/', ' ',
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage(
						'content',
						'Content-Type: text/plain; charset=utf-8' . PHP_EOL .
						'Content-Transfer-Encoding: 7bit'
					),
					__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt',
					'boundary'
				))->content()
			)
		);
	}

	public function testThrowingNonexistentAttachmentException() {
		Assert::exception(
			function() {
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage(
						'content',
						'headers'
					), 'nonexistent/path'
				))->content();
			},
			\UnexpectedValueException::class,
			'Attached file does not exist'
		);
	}
}

(new MessageWithAttachment())->run();