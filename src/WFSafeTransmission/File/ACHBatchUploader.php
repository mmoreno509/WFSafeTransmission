<?php

/**
 * Uploads an ACHBatch file to the wells fargo server
 */
class ACHBatchUploader extends \App\libraries\WFPositivePay\WellsFargoUploader {

	/**
	 * The ACHFile object to upload to wells fargo.
	 * 
	 * @var [type]
	 */
	protected $file;

	public function __construct( ACHFile $file )
	{
		// get file
		$this->file = $file;

        parent::__construct();
	}

	protected function getFileLocation()
    {
        return $this->file->getFileLocation();
    }

	protected function getUploadFolder()
    {
        return 'inbound/LDTEL926_ACH_4/';
    }

    public function upload()
    {
        $response = parent::upload();

        \Event::fire('ach-batch.uploaded', [$response, $this->file]);

        return $response;
    }

}