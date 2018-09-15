<?php
declare(strict_types = 1);

namespace Dasuos\Mail\Unit;

use Dasuos\Mail;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */

final class MessageWithAttachment extends \Tester\TestCase {

	public function testReturningMixedContentType() {
		Assert::same(
			['Content-Type' => 'multipart/mixed; boundary="random"'],
			preg_replace(
				'~[0-9a-z]{20}~',
				'random',
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage('content', ['header' => 'value']),
					__DIR__ . '/../Fixtures/attachment.txt'
				))->headers()
			)
		);
	}

	public function testReturningPlainTextWithAttachment() {
		Assert::same(
			preg_replace(
				'~\s+~',
				' ',
				'--boundary
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
				
				content 
				
				--boundary 
				Content-Type: application/octet-stream; name="attachment.txt" 
				Content-Transfer-Encoding: base64 
				Content-Disposition: attachment; filename="attachment.txt" 
				
				Zm9yIHRlc3RpbmcgcHVycG9zZQ== 
				
				--boundary--'
			),
			preg_replace(
				'~[0-9a-z]{20}~',
				'boundary',
				preg_replace(
					'~\s+~',
					' ',
					(new Mail\MessageWithAttachment(
						new Mail\FakeMessage(
							'content',
							[
								'Content-Type' => 'text/plain; charset=utf-8',
								'Content-Transfer-Encoding' => '7bit',
							]
						),
						__DIR__ . '/../Fixtures/attachment.txt'
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
					),
					'nonexistent/path'
				))->content();
			},
			\UnexpectedValueException::class,
			'Attached file does not exist'
		);
	}
}

(new MessageWithAttachment())->run();