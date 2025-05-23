<?php

namespace Ratchet\RFC6455\Test\Unit\Handshake;

use Ratchet\RFC6455\Handshake\PermessageDeflateOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers Ratchet\RFC6455\Handshake\PermessageDeflateOptions
 */
class PermessageDeflateOptionsTest extends TestCase
{
    public static function versionSupportProvider(): array {
        return [
            ['7.0.17', false],
            ['7.0.18', true],
            ['7.0.200', true],
            ['5.6.0', false],
            ['7.1.3', false],
            ['7.1.4', true],
            ['7.1.200', true],
            ['10.0.0', true]
        ];
    }

    /**
     * @requires function deflate_init
     * @dataProvider versionSupportProvider
     */
    public function testVersionSupport(string $version, bool $supported): void {
        $this->assertEquals($supported, PermessageDeflateOptions::permessageDeflateSupported($version));
    }
}
