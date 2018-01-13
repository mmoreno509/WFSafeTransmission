<?php
namespace WFSafeTransmission\File;

class ACHBatch {
	
	protected $id;
    /**
     * @var BatchHeader
     */
	protected $header 		= null;	
	protected $entryDetails = array();
	protected $addendas 	= array();
	protected $control		= null;

	protected $totalDebit 	= 0;
	protected $totalCredit	= 0;

	public function __construct(BatchHeader $header) {
		$this->id = rand();
		$this->setHeader($header);
	}

	/**
	 * gets created batch records unique identifier
	 * @return int integer of given time batch record was created
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the file header
	 * @param ACHRecord $record [description]
	 */
	public function setHeader(BatchHeader $record) {
		$this->header = $record;

		return $this;
	}

	public function getHeader() {
		return $this->header;
	}

	public function getTotalDebit() {
		return $this->totalDebit;
	}

	public function getTotalCredit() {
		return $this->totalCredit;
	}

	public function setBatchNumber( $number ) {
		$this->header->setBatchNumber( $number );

		return $this;
	}

	/**
	 * Adds a detail record to the Batch File. Keeps control record of total credits
	 * and debits added.
	 * @param ACHRecord $record Record Detail to be added to the batch
	 */
	public function addEntryDetail(EntryDetail $record) {
		// validate the record.
		$record->validate();

		switch ( $record->getTransactionCode()->getType() ) {
			case TransactionCode::CREDIT:
				$this->totalCredit += $record->getAmount();
				break;
			case TransactionCode::DEBIT:
				$this->totalDebit += $record->getAmount();
				break;
		}
		
		// set the trace number on the EntryDetail
		$record->setTraceNumber( count($this->entryDetails) + 1 );
		
		// add it to the detail list
		$this->entryDetails[ $record->getId() ] = $record;
		array_merge($this->addendas, $record->getAddendas());

		return $this;
	}

	/**
	 * Returns the total number of entries and addendas within the batch.
	 * @return [type] [description]
	 */
	public function getEntryCount() {
		return count($this->entryDetails) + count($this->addendas); 
	}

	/**
	 * Checks if the current batch is open.
	 * @return boolean [description]
	 */
	public function isOpen(){
		return is_null($this->control);
	}

	/**
	 * Batch Control Record. Closes the batch record
	 * @param ACHRecord $record [description]
	 */
	public function close( ) {
		$this->control = new BatchControl($this);
		return $this;
	}

	public function validate() {
		// required headers
		if( is_null($this->header)) {
			throw new Exception('Batch Header Record Required.');
		}

		// required 1 detail
		if( empty($this->entryDetails)){
			throw new Exception('At least 1 detail record is required');
		}
	}

	/**
	 * Returns a prepared batch file.
	 * @return [type] [description]
	 */
	public function getData() {
		// Validate the batch record to be sure it has necessary controls.
		$this->validate();

		// adds batch header and new line
		$batch = $this->header->getData();

		// prints all entryDetails,ew line after each
		foreach ($this->entryDetails as $detail) {
			$batch .= $detail->getData();
			foreach ($detail->getAddendas() as $addenda) {
				$batch .= $addenda->getData();
			}
		}

		// adds batch control, new line after each.
		$batch .= $this->control->getData();

		// returns the compiled batch file.
		return $batch;
	}

}