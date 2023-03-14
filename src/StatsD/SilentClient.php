<?php
namespace HelloFresh\Stats\StatsD;

use League\StatsD\Client;
use League\StatsD\Exception\ConnectionException;

class SilentClient extends Client
{
    /**
     * @inheritdoc
     */
    protected function send(array $data, array $tags = []) : Client
    {
        $tagsData = $this->serializeTags(array_replace($this->tags, $tags));

        try {
            $socket = $this->getSocket();
            $messages = [];
            $prefix = $this->namespace ? $this->namespace . '.' : '';
            foreach ($data as $key => $value) {
                $messages[] = $prefix . $key . ':' . $value . $tagsData;
            }
            $this->message = implode("\n", $messages);
            @fwrite($socket, $this->message);
            fflush($socket);
        } catch (ConnectionException $e) {
            if ($this->throwConnectionExceptions) {
                throw $e;
            }
        }

        return $this;
    }
}
