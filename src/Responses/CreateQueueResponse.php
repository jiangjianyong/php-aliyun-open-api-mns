<?php
namespace Aliyun\MNS\Responses;

use Aliyun\MNS\Common\XMLParser;
use Aliyun\MNS\Constants;
use Aliyun\MNS\Exception\InvalidArgumentException;
use Aliyun\MNS\Exception\MnsException;
use Aliyun\MNS\Exception\QueueAlreadyExistException;

class CreateQueueResponse extends BaseResponse
{
    private $queueName;

    public function __construct($queueName)
    {
        $this->queueName = $queueName;
    }

    public function parseResponse($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        if ($statusCode == 201 || $statusCode == 204) {
            $this->succeed = true;
        } else {
            $this->parseErrorResponse($statusCode, $content);
        }
    }

    public function parseErrorResponse($statusCode, $content, MnsException $exception = null)
    {
        $this->succeed = false;
        $xmlReader = $this->loadXmlContent($content);

        try {
            $result = XMLParser::parseNormalError($xmlReader);

            if ($result['Code'] == Constants::INVALID_ARGUMENT) {
                throw new InvalidArgumentException($statusCode, $result['Message'], $exception, $result['Code'],
                    $result['RequestId'], $result['HostId']);
            }
            if ($result['Code'] == Constants::QUEUE_ALREADY_EXIST) {
                throw new QueueAlreadyExistException($statusCode, $result['Message'], $exception, $result['Code'],
                    $result['RequestId'], $result['HostId']);
            }
            throw new MnsException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'],
                $result['HostId']);
        } catch (\Exception $e) {
            if ($exception != null) {
                throw $exception;
            } elseif ($e instanceof MnsException) {
                throw $e;
            } else {
                throw new MnsException($statusCode, $e->getMessage());
            }
        } catch (\Throwable $t) {
            throw new MnsException($statusCode, $t->getMessage());
        }
    }

    public function getQueueName()
    {
        return $this->queueName;
    }
}

?>
