<?php

declare(strict_types=1);

namespace AsyncAws\Flysystem\S3\Tests\Unit;

use AsyncAws\Core\Response;
use AsyncAws\Flysystem\S3\S3FilesystemV2;
use AsyncAws\S3\Result\PutObjectOutput;
use AsyncAws\S3\S3Client;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;

class S3FilesystemV2Test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!interface_exists(FilesystemAdapter::class)) {
            self::markTestSkipped('Flysystem v2 is not installed');
        }
    }

    public function testWrite()
    {
        $file = 'foo/bar.txt';
        $prefix = 'all-files';
        $bucket = 'foobar';

        if (\class_exists(Response::class)) {
            $client = new MockHttpClient();
            $result = new PutObjectOutput(new Response($client->request('POST', 'http://localhost'), $client, new NullLogger()));
        } else {
            $client = new MockHttpClient();
            $result = new PutObjectOutput($client->request('POST', 'http://localhost'), $client);
        }

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['putObject'])
            ->getMock();
        $s3Client->expects(self::once())
            ->method('putObject')
            ->with(self::callback(function (array $input) use ($file, $prefix, $bucket) {
                if ($input['Key'] !== $prefix . '/' . $file) {
                    return false;
                }
                if ('contents' !== $input['Body']) {
                    return false;
                }
                if ($input['Bucket'] !== $bucket) {
                    return false;
                }

                return true;
            }))->willReturn($result);

        $filesystem = new S3FilesystemV2($s3Client, $bucket, $prefix);
        $filesystem->write($file, 'contents', new Config());
    }
}
