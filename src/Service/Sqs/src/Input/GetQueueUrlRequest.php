<?php

namespace AsyncAws\Sqs\Input;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Input;
use AsyncAws\Core\Request;
use AsyncAws\Core\Stream\StreamFactory;

final class GetQueueUrlRequest extends Input
{
    /**
     * The name of the queue whose URL must be fetched. Maximum 80 characters. Valid values: alphanumeric characters,
     * hyphens (`-`), and underscores (`_`).
     *
     * @required
     *
     * @var string|null
     */
    private $QueueName;

    /**
     * The AWS account ID of the account that created the queue.
     *
     * @var string|null
     */
    private $QueueOwnerAWSAccountId;

    /**
     * @param array{
     *   QueueName?: string,
     *   QueueOwnerAWSAccountId?: string,
     *   @region?: string,
     * } $input
     */
    public function __construct(array $input = [])
    {
        $this->QueueName = $input['QueueName'] ?? null;
        $this->QueueOwnerAWSAccountId = $input['QueueOwnerAWSAccountId'] ?? null;
        parent::__construct($input);
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getQueueName(): ?string
    {
        return $this->QueueName;
    }

    public function getQueueOwnerAWSAccountId(): ?string
    {
        return $this->QueueOwnerAWSAccountId;
    }

    /**
     * @internal
     */
    public function request(): Request
    {
        // Prepare headers
        $headers = ['content-type' => 'application/x-www-form-urlencoded'];

        // Prepare query
        $query = [];

        // Prepare URI
        $uriString = '/';

        // Prepare Body
        $body = http_build_query(['Action' => 'GetQueueUrl', 'Version' => '2012-11-05'] + $this->requestBody(), '', '&', \PHP_QUERY_RFC1738);

        // Return the Request
        return new Request('POST', $uriString, $query, $headers, StreamFactory::create($body));
    }

    public function setQueueName(?string $value): self
    {
        $this->QueueName = $value;

        return $this;
    }

    public function setQueueOwnerAWSAccountId(?string $value): self
    {
        $this->QueueOwnerAWSAccountId = $value;

        return $this;
    }

    private function requestBody(): array
    {
        $payload = [];
        if (null === $v = $this->QueueName) {
            throw new InvalidArgument(sprintf('Missing parameter "QueueName" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['QueueName'] = $v;
        if (null !== $v = $this->QueueOwnerAWSAccountId) {
            $payload['QueueOwnerAWSAccountId'] = $v;
        }

        return $payload;
    }
}
