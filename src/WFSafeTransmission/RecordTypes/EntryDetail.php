<?php

class EntryDetail extends ACHRecord {
	protected static $RecordType = '6';

	protected $transactionCode		= null;
	protected $RTNumber 			= null;
	protected $RTcheckDigit 		= null;
	protected $accountNumber 		= null;
	protected $individualId 		= null;
	protected $individualName 		= null;
	protected $amount 				= null;
	protected $discretionaryData 	= null;
	protected $addendaRecord 		= null;
	protected $traceNumber 			= null;
	protected $sequenceNumber 		= null;
	protected $addendas 			= array();


	public function setDefaults() {
		// Record Type
		$this->addField(new ACHRecordField(1, static::$RecordType));
		$this->setDisretionaryData();
		$this->hasAddendaRecord(false);
	}

	public function setTransactionCode(TransactionCode $code) {
		$this->transactionCode = $code;
		$this->addField(new ACHRecordField([2,3], $code->getCode()));

		return $this;
	}

	public function getTransactionCode(){
		return $this->transactionCode;
	}

	/**
	 * The receiving routing number.
	 * @param string $number Numeric starting w/ 0,1,2, or 3
	 */
	public function setRecievingRTNumber( $number ) {
		// convert the number to a string to later evaluate the first digit.
		$this->RTNumber = strval($number);
		$this->addField(new ACHRecordField([4,12], $this->RTNumber));
		
		return $this;
	}

	/**
	 * Receiving account number. 
	 * @param string $number Alphanumeric
	 */
	public function setRecievingAccountNumber( $number ) {
		$this->accountNumber = strval($number);
		$field = new ACHRecordField([13,29], $this->accountNumber);
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Sets entry detail amount. Negative numbers are converted to positive, 
	 * decimals are stripped, and leading zeros are added for padding.
	 * EX: -18.50 = 000001850
	 * @param string $amount Numeric
	 */
	public function setAmount( $amount ) {
		// removes non-negative values and sets a calculable amount for the class to use.
		(float)$this->amount = (float) abs($amount);

		// Create field object.
		$field = new ACHRecordField([30,39], static::formatDecimal($amount));
		$field->addPadding('0');

		// add the field to the record.
		$this->addField( $field );

		return $this;
	}

	public function getAmount(){
		return $this->amount;
	}

	/**
	 * Individual ID. (required for SEC code ARC, POP, or RCK)
	 * @param string $id Alphanumeric.
	 */
	public function setIndividualId( $id ) {
		$this->individualId = $id;
		$field = new ACHRecordField([40,54], $this->individualId);
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Set individual name.
	 * @param string $name Alphanumeric
	 */
	public function setIndividualName( $name ) {
		$this->individualName = $name;
		$field = new ACHRecordField([55,76], $name );
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Discretionary data
	 * @param string $data Two spaces or (Rb or Sb (b=space)) for WEB
	 */
	public function setDisretionaryData( $data = null ) {
		$this->discretionaryData = $data;
		
		if( !is_null($this->discretionaryData) )
			$this->discretionaryData[1] = ' ';
		
		$this->addField( new ACHRecordField([77,78], $this->discretionaryData));

		return $this;
	}

	/**
	 * Addenda Record indicator.
	 * @param [type] $record 1 if addenda record exists.
	 */
	private function hasAddendaRecord( $record = false) {
		// if true, set addenda record to 1
		$this->addendaRecord = ($record) ? '1' : '0';
		$this->addField( new ACHRecordField(79, $this->addendaRecord));
	}

	public function addAddenda( Addenda $record ) {		
		// the referring entry detail to add to the addenda.
		$record->setSequenceNumber( count($this->addendas) + 1 );
		$this->addendas[ $record->getId() ] = $record;
		$this->hasAddendaRecord(true);

		return $this;
	}

	public function getAddendas(){
		return $this->addendas;
	}

	/**
	 * returns the total number of addendas added to the entry detail
	 * @return [type] [description]
	 */
	public function getTotalAddendas(){
		return count($this->addendas);
	}

	/**
	 * Sets the trace number, pass the incrementing sequence of this entry detail, will concatenate the
	 * required numbers to the rest of the sequence.
	 * @param [type] $number [description]
	 */
	public function setTraceNumber( $sequenceNumber ) {
		// set the sequence number
		$this->sequenceNumber = strval($sequenceNumber);
		$this->sequenceNumber = str_pad($this->sequenceNumber, (8 - strlen($this->sequenceNumber)), '0', STR_PAD_LEFT);

		// create the trace number from the sequence number
		$this->traceNumber = self::WELLS_FARGO_RT_NUMBER . $this->sequenceNumber;
		$this->addField( new ACHRecordField([80,94], $this->traceNumber) );

		// update the addendas with the entry detail sequence number
		$this->setAddendaSequenceNumbers();
		return $this;
	}

	private function setAddendaSequenceNumbers() {
		foreach($this->addendas as $addenda){
			// the sequence number to give to the addenda
			$addenda->setEntryDetailSequenceNumber( $this->sequenceNumber );
		}
	}

	public function validate() {
		if(empty($this->RTNumber) or !in_array($this->RTNumber[0], [0,1,2,3]))
			throw new Exception('Receiving DFI Routing No. is required in EntryDetail and must begin with 0,1,2 or 3. Given: '. $this->RTNumber);

		if(empty($this->amount))	
			throw new Exception('Amount is required in EntryDetail.');

		if( empty($this->accountNumber))
			throw new Exception('Account Number is required in EntryDetail.');
			
		if( empty($this->transactionCode))
			throw new Exception('Transaction code is required in EntryDetail.');

		if( empty($this->individualId))
			throw new Exception('Individual Id is required in EntryDetail.');
			
		if( empty($this->individualName))
			throw new Exception('Individual name is required in EntryDetail.');
			
	}
}