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
				'multipart/mixed; boundary="random"'
			)],
			array_map(
				function(string $value): string {
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
					'--boundary
					Content-Type: text/plain; charset=utf-8 
					Content-Transfer-Encoding: 7bit 
				
					content 
				
					--boundary 
					Content-Type: application/octet-stream; name="attachment.txt" 
					Content-Transfer-Encoding: base64 
					Content-Disposition: attachment; filename="attachment.txt" 
				
					dGVzdGluZyBjb250ZW50 
				
					--boundary--'
				)
			),
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
				)
			)
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