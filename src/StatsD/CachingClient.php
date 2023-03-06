<?php
namespace HelloFresh\Stats\StatsD;

use League\StatsD\Client;
use League\StatsD\Exception\ConnectionException;

class CachingClient extends Client
{
    /** @var resource */
    protected $socket;

    /**
     * @throws ConnectionException
     * @return resource
     */
    protected function getSocket()
    {
        if (!$this->socket) {
            $this->socket = @fsockopen('udp://' . $this->host, $this->port, $errno, $errstr, $this->timeout);
            if (!$this->socket) {
                throw new ConnectionException($this, '(' . $errno . ') ' . $errstr);
            }
        }

        return $this->socket;
    }

    /**
     * Send Data to StatsD Server
     * @param  array               $data A list of messages to send to the server
     * @throws ConnectionException If there is a connection problem with the host
     * @return $this
     */
    protected function send(array $data)
    {
        try {
            $socket = $this->getSocket();
            $messages = [];
            $prefix = $this->namespace ? $this->namespace . '.' : '';
            foreach ($data as $key => $value) {
                $messages[] = $prefix . $key . ':' . $value;
            }
            $this->message = implode("\n", $messages);
            fwrite($socket, $this->message);
            fflush($socket);
        } catch (\Exception $e) {
            if ($this->throwConnectionExceptions) {
                throw $e;
            } else {
                trigger_error(
                    sprintf('StatsD server connection failed (udp://%s:%d)', $this->host, $this->port),
                    E_USER_WARNING
                );
            }
        }

        return $this;
    }

    public function __destruct()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }
}
