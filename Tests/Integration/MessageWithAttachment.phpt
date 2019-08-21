<?php
declare(strict_types = 1);

namespace Dasuos\Mail\Integration;

use Dasuos\Mail;
use Dasuos\Mail\Misc\SequentialBoundaries;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */

final class MessageWithAttachment extends \Tester\TestCase {

	private const HTML_BOUNDARY_SEQUENCE = [1, 2, 3, 4];
	private const ATTACHMENT_BOUNDARY_SEQUENCE = [0, 5, 6];
	private const BOUNDARIES = 0;

	public function sequences() {
		return [
			[self::HTML_BOUNDARY_SEQUENCE],
			[self::ATTACHMENT_BOUNDARY_SEQUENCE],
		];
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
						new Mail\PlainMessage('content'),
						__DIR__ . '/../Fixtures/attachment.txt'
					))->content()
				)
			)
		);
	}

	public function testReturningHtmlWithAttachment() {
		Assert::same(
			preg_replace(
				'~\s+~',
				' ',
				'--boundary
				Content-Type: multipart/alternative; boundary="boundary"
				
				--boundary
				Content-Type: text/plain; charset=utf-8
				Content-Transfer-Encoding: 7bit
				
				\ntitle\n\ncontent\n
				
				--boundary
				Content-Type: text/html; charset=utf-8
				Content-Transfer-Encoding: 7bit
				
				<h1>title</h1><p>content</p>
				
				--boundary--
				
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
						new Mail\HtmlMessage('<h1>title</h1><p>content</p>'),
						__DIR__ . '/../Fixtures/attachment.txt'
					))->content()
				)
			)
		);
	}

	/**
	 * @dataProvider sequences
	 */
	public function testReturningValidBoundarySequence(array $sequence) {
		preg_match_all(
			'~[0-9a-z]{20}~',
			(new Mail\MessageWithAttachment(
				new Mail\HtmlMessage('<h1>title</h1><p>content</p>'),
				__DIR__ . '/../Fixtures/attachment.txt'
			))->content(),
			$matches
		);
		Assert::true(
			(new SequentialBoundaries(
				$matches[self::BOUNDARIES],
				$sequence
			))->identical()
		);
	}

	public function testThrowingOnNonexistentAttachedFile() {
		Assert::exception(
			function() {
				(new Mail\MessageWithAttachment(
					new Mail\HtmlMessage('<h1>title</h1><p>content</p>'),
					__DIR__ . '/../Fixtures/nonexistent.txt'
				))->content();
			},
			\UnexpectedValueException::class,
			'Attached file does not exist'
		);
	}
}

(new MessageWithAttachment())->run();
