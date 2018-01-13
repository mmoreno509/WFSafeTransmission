<?php
namespace WFSafeTransmission\File;

class ACHFile {

	protected $id;
	protected $fileName;
	protected $filePath;
	protected $header;
	protected $batches;
	protected $control;

	protected $totalCredit = 0;
	protected $totalDebit = 0;
	protected $totalEntries = 0;

	public function __construct(FileHeader $header = null, $file = null, $path = null){
		if( is_null($header) )
			$header = new FileHeader;
		$this->setHeader($header);
		$this->setFilePath($path);
		$this->setFileName( $file );
		$this->id = time();
	}

	// Unique file identifier.
	public function getId() {
		return $this->id;
	}

	public function setFilePath( $path = null ){
		if(is_null($path)){
			$path = public_path();
		}
		$this->filePath = $path;
	}

	public function getFileLocation() {
		return $this->filePath . $this->fileName;
	}

	/**
	 * Sets the ACH File name with given var, or the default defined value.
	 * @param string $file The name to save batch file as.
	 */
	public function setFileName( $file = null){
		if(is_null($file)){
			$file = 'ACHFile_' . date('Y_m_d_H_i') . '.txt';
		}

		$this->fileName = $file;
	}

	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * Sets the header record for the file.
	 * @param ACHRecord $record [description]
	 */
	public function setHeader( FileHeader $record ) {
		$this->header = $record;
	}

	/**
	 * Returns the number of 
	 * @return [type] [description]
	 */
	public function getHeader(){
		return $this->header;
	} 

	/**
	 * Adds batch records to the file.
	 * @param ACHBatch $batch [description]
	 */
	public function addBatch(ACHBatch $batch) {
		$batch->validate();

		// Sum total credits
		$this->totalCredit += $batch->getTotalCredit();

		// Sum total debits
		$this->totalDebit += $batch->getTotalDebit();

		// Sum total entries
		$this->totalEntries += $batch->getEntryCount();

		$batch
			->setBatchNumber( count($this->batches) + 1)
			->close();

		// add the batch record
		$this->batches[ $batch->getId() ] = $batch;
	}

	/**
	 * Returns the number of batches that have been added to the file.
	 * @return [type] [description]
	 */
	public function getBatchCount() {
		return count( $this->batches );
	}

	public function getTotalCredit(){
		return $this->totalCredit;
	}

	public function getTotalDebit(){
		return $this->totalDebit;
	}

	public function getTotalEntries(){
		return $this->totalEntries;
	}

	/**
	 * Adds a control record to the file.
	 * @param ACHRecord $record [description]
	 */
	public function close() {
		$this->control = new FileControl( $this );
	}

	/**
	 * Sets validation rules to check for, throws exceptions on error.
	 * @return [type] [description]
	 */
	private function validate() {
		if( empty($this->header) )
			throw new Exception('ACH file header record is required.');

		if( empty($this->batches) )
			throw new Exception('ACH file requires at least 1 batch record.');

		if( empty($this->control))
			throw new Exception('ACH file control record is required.');			
	}

	public function getData() {
		// validate the file components
		$this->validate();

		// add file header record
		$data = $this->header->getData();

		// add file batch records
		foreach ($this->batches as $batch) {
			$data .= $batch->getData();
		}
		// add file control record.
		$data .= $this->control->getData();

		return $data;
	}

	/**
	 * Creates a new ACH file with the given file name or a default
	 * ACHFile_YYYY_MM_DD_HH_MM.txt in /tmp/
	 * 
	 * @return string string location of written file name.
	 */
	public function write(){

		$ACHFile = fopen($this->getFileLocation(), "w");
		fwrite($ACHFile, $this->getData());
		fclose($ACHFile);

		return $this->getFileLocation();	
	}
}