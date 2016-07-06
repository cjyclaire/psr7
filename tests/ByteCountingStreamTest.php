<?php
namespace GuzzleHttp\Tests\Psr7;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\ByteCountingStream;

/**
 * @covers GuzzleHttp\Psr7\ByteCountingStream
 */
class ByteCountingStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Bytes to read should be non-negative integer, got
     */
    public function testEnsureNonNegativeByteCount()
    {
        new ByteCountingStream(Psr7\stream_for('testing'), -2);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Bytes to read should be less than or equal to stream size
     */
    public function testEnsureValidByteCountNumber()
    {
        new ByteCountingStream(Psr7\stream_for('testing'), 10);
    }

    public function testByteCountingReadWhenAvailable()
    {
        $testStream = new ByteCountingStream(Psr7\stream_for('foo bar test'), 8);
        $this->assertEquals('foo ', $testStream->read(4));
        $this->assertEquals('bar ', $testStream->read(4));
        $this->assertEquals('', $testStream->read(4));
        $testStream->close();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough bytes to read from position :
     */
    public function testEnsureStopReadWhenHitEof()
    {
        $test = new ByteCountingStream(Psr7\stream_for('testing'), 6);
        $test->seek(4);
        $test->read(4);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Fail to read
     */
    public function testEnsureReadWithRange()
    {
        $test = new ByteCountingStream(Psr7\stream_for('testing'), 6);
        $this->assertEquals('test', $test->read(4));
        $test->read(4);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The stream is detached
     */
    public function testEnsureReadUnclosedStream()
    {
        $body = Psr7\stream_for("closed");
        $closedStream = new ByteCountingStream($body, 5);
        $body->close();
        $closedStream->read(3);
    }
}
