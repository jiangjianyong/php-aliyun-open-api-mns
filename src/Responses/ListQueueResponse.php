<?php
namespace Aliyun\MNS\Responses;

use Aliyun\MNS\Common\XMLParser;
use Aliyun\MNS\Exception\MnsException;

class ListQueueResponse extends BaseResponse
{
    private $queueNames;
    private $nextMarker;

    public function __construct()
    {
        $this->queueNames = array();
        $this->nextMarker = null;
    }

    public function isFinished()
    {
        return $this->nextMarker == null;
    }

    public function getQueueNames()
    {
        return $this->queueNames;
    }

    public function getNextMarker()
    {
        return $this->nextMarker;
    }

    public function parseResponse($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        if ($statusCode != 200) {
            $this->parseErrorResponse($statusCode, $content);
            return;
        }

        $this->succeed = true;
        $xmlReader = $this->loadXmlContent($content);

        try {
            while ($xmlReader->read()) {
                if ($xmlReader->nodeType == \XMLReader::ELEMENT) {
                    switch ($xmlReader->name) {
                        case 'QueueURL':
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $queueName = $this->getQueueNameFromQueueURL($xmlReader->value);
                                $this->queueNames[] = $queueName;
                            }
                            break;
                        case 'NextMarker':
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $this->nextMarker = $xmlReader->value;
                            }
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new MnsException($statusCode, $e->getMessage(), $e);
        } catch (\Throwable $t) {
            throw new MnsException($statusCode, $t->getMessage());
        }
    }

    public function parseErrorResponse($statusCode, $content, MnsException $exception = null)
    {
        $this->succeed = false;
        $xmlReader = $this->loadXmlContent($content);

        try {
            $result = XMLParser::parseNormalError($xmlReader);

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

    private function getQueueNameFromQueueURL($queueURL)
    {
        $pieces = explode("/", $queueURL);
        if (count($pieces) == 5) {
            return $pieces[4];
        }
        return "";
    }
}

?>
