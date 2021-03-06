<?php
namespace Aliyun\MNS\Model;

use Aliyun\MNS\Constants;

/**
 * Please refer to
 * https://docs.aliyun.com/?spm=#/pub/mns/api_reference/intro&intro
 * for more details
 */
class TopicAttributes
{
    private $maximumMessageSize;
    private $messageRetentionPeriod;
    private $LoggingEnabled;

    # the following attributes cannot be changed
    private $topicName;
    private $createTime;
    private $lastModifyTime;

    public function __construct(
        $maximumMessageSize = null,
        $messageRetentionPeriod = null,
        $topicName = null,
        $createTime = null,
        $lastModifyTime = null,
        $LoggingEnabled = null
    ) {
        $this->maximumMessageSize = $maximumMessageSize;
        $this->messageRetentionPeriod = $messageRetentionPeriod;
        $this->loggingEnabled = $LoggingEnabled;

        $this->topicName = $topicName;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }

    static public function fromXML(\XMLReader $xmlReader)
    {
        $maximumMessageSize = null;
        $messageRetentionPeriod = null;
        $topicName = null;
        $createTime = null;
        $lastModifyTime = null;
        $loggingEnabled = null;

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType == \XMLReader::ELEMENT) {
                switch ($xmlReader->name) {
                    case 'MaximumMessageSize':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $maximumMessageSize = $xmlReader->value;
                        }
                        break;
                    case 'MessageRetentionPeriod':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $messageRetentionPeriod = $xmlReader->value;
                        }
                        break;
                    case 'TopicName':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $topicName = $xmlReader->value;
                        }
                        break;
                    case 'CreateTime':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $createTime = $xmlReader->value;
                        }
                        break;
                    case 'LastModifyTime':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $lastModifyTime = $xmlReader->value;
                        }
                        break;
                    case 'LoggingEnabled':
                        $xmlReader->read();
                        if ($xmlReader->nodeType == \XMLReader::TEXT) {
                            $loggingEnabled = $xmlReader->value;
                            if ($loggingEnabled == "True") {
                                $loggingEnabled = true;
                            } else {
                                $loggingEnabled = false;
                            }
                        }
                        break;
                }
            }
        }

        $attributes = new TopicAttributes(
            $maximumMessageSize,
            $messageRetentionPeriod,
            $topicName,
            $createTime,
            $lastModifyTime,
            $loggingEnabled);
        return $attributes;
    }

    public function getMaximumMessageSize()
    {
        return $this->maximumMessageSize;
    }

    public function setMaximumMessageSize($maximumMessageSize)
    {
        $this->maximumMessageSize = $maximumMessageSize;
    }

    public function getLoggingEnabled()
    {
        return $this->loggingEnabled;
    }

    public function setLoggingEnabled($loggingEnabled)
    {
        $this->loggingEnabled = $loggingEnabled;
    }

    public function getMessageRetentionPeriod()
    {
        return $this->messageRetentionPeriod;
    }

    public function setMessageRetentionPeriod($messageRetentionPeriod)
    {
        $this->messageRetentionPeriod = $messageRetentionPeriod;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function getCreateTime()
    {
        return $this->createTime;
    }

    public function getLastModifyTime()
    {
        return $this->lastModifyTime;
    }

    public function writeXML(\XMLWriter $xmlWriter)
    {
        if ($this->maximumMessageSize != null) {
            $xmlWriter->writeElement(Constants::MAXIMUM_MESSAGE_SIZE, $this->maximumMessageSize);
        }
        if ($this->messageRetentionPeriod != null) {
            $xmlWriter->writeElement(Constants::MESSAGE_RETENTION_PERIOD, $this->messageRetentionPeriod);
        }
        if ($this->loggingEnabled !== null) {
            $xmlWriter->writeElement(Constants::LOGGING_ENABLED, $this->loggingEnabled ? "True" : "False");
        }
    }
}

?>
