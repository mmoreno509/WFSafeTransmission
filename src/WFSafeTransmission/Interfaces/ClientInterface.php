<?php
namespace WFSafeTransmission\Interfaces;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;

/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:08 PM
 */
interface ClientInterface
{
    public function setHttpClient(HttpClient $client);

    public function getHttpClient();

    public function sendRequest(RequestInterface $message);
}