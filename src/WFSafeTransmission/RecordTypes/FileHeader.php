<?php

class FileHeader extends ACHRecord {
	
	protected static $RecordType = '1';
	
	protected $fileId;
	protected $fileModifier;
	protected $companyName;
	protected $referenceCode;

	public function __construct($FileMod = 'A'){
		parent::__construct();
		$this->setFileModifier($FileMod);
	}

	public function getId() {
		return $this->fileId;
	}

	/**
	 * Sets file Id for ACH document header.
	 * @param string $id Wells Fargo assigned id.
	 */
	private function setFileId($id){
		$this->fileId = $id;
		$this->addField(new ACHRecordField([14,23], $this->fileId));

		return $this;
	}

	/**
	 * Set File Modifier
	 * @param string $mod character file id modifier
	 */
	public function setFileModifier( $mod ) {
		$this->fileModifier = $mod;
		$this->addField(new ACHRecordField(34, $this->fileModifier));

		return $this;
	}

	/**
	 * Set optional reference code in ACH file header
	 * @param [type] $code [description]
	 */
	public function setReferenceCode( $code ) {
		$this->referenceCode = $code;
		$this->addField(new ACHRecordField([87,94], $this->referenceCode));

		return $this;
	}

	/**
	 * Sets default and constanct fields
	 */
	public function setDefaults() {
		// Record type
		$this->addField(new ACHRecordField(1, static::$RecordType));
		
		// Priority Code
		$this->addField(new ACHRecordField([2,3], '01'));
		
		// Routing Number = Constant b091000019, (b=space)
		$rtNumber = new ACHRecordField([4,13], self::WELLS_FARGO_FILE_RT_NUMBER );
		$rtNumber->addPadding(' ', STR_PAD_LEFT);
		$this->addField( $rtNumber );

		// Set File ID constant
		$this->setFileId( self::FILE_ID );

		// Creating Date
		$this->addField(new ACHRecordField([24,29], date('Ymd')));
		
		// Creation Time
		$this->addField(new ACHRecordField([30,33], date('Hi')));
		
		// RecordSize
		$this->addField(new ACHRecordField([35,37], self::RECORD_SIZE));
		
		// Blocking Factor
		$this->addField(new ACHRecordField([38,39], static::BLOCKING_FACTOR));
		
		// Format code
		$this->addField(new ACHRecordField(40, self::FORMAT_CODE));
		
		// Origination Bank
		$this->addField(new ACHRecordField([41,63], self::ORIGINATION_BANK));
		
		// Set Company Name
		$this->addField(new ACHRecordField([64,86], 'NEXOGY'));
	}

	public function validate(){
		if( empty($this->fileId) )
			throw new Exception('File id is required');
		if( empty($this->fileModifier))
			throw new Exception('File id modifier is required');
	}
}