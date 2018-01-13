<?php
namespace WFSafeTransmission\HttpClient;

use Http\Discovery\HttpClientDiscovery;
use Http\Client\HttpClient;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use WFSafeTransmission\Interfaces\ClientInterface;

/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:02 PM
 */
class Client implements ClientInterface
{
    /**
     * @var HttpClient;
     */
    protected $client;

    public function setHttpClient( HttpClient $client = null)
    {
        $this->client = $client ?: HttpClientDiscovery::find();
    }

    public function getHttpClient()
    {
        return $this->client;
    }

    public function sendRequest( RequestInterface $psr7Request)
    {
        $this->client->sendRequest($psr7Request);
    }
}