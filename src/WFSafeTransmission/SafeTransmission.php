<?php
namespace WFSafeTransmission;
use WFSafeTransmission\File\ACHBatch;
use WFSafeTransmission\File\ACHFile;
use WFSafeTransmission\File\BatchHeader;
use WFSafeTransmission\HttpClient\Client;

/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:00 PM
 */
class SafeTransmission
{
    const VERSION = '1.0.0';

    const CONFIG_CERT = 'cert';
    const CONFIG_USERNAME = 'username';
    const CONFIG_PASSWORD = 'password';
    const CONFIG_URL = 'url';
    const CONFIG_STORE_PATH = 'store_path';

    protected $config;
    protected $client;

    /**
     * @var ACHFile;
     */
    protected $file;
    protected $batch;

    public function __construct(array $config)
    {
        $this->config = array_merge([
            'cert' => getenv(static::CONFIG_CERT),
            'username' => getenv(static::CONFIG_USERNAME),
            'password' => getenv(static::CONFIG_PASSWORD),
            'url' => getenv(static::CONFIG_URL),
            'store_path' => getenv(static::CONFIG_STORE_PATH),
            'http_client' => null
        ],$config);

        $this->client = new Client($config['http_client']);
    }

    public function openFile() {
        $this->file = new ACHFile();
        $this->file->setFilePath($this->config[static::CONFIG_STORE_PATH]);
        return $this->file;
    }

    public function openBatch()
    {
        $this->batch = new ACHBatch(new BatchHeader());
    }

    public function closeBatch(ACHBatch $batch)
    {
        if($this->batch)
            $this->file->addBatch($this->batch);

        $this->batch = null;
    }

    public function closeFile()
    {
        $this->file->close();
    }

    public function upload()
    {

    }
}