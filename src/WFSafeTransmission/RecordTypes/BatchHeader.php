<?php
namespace WFSafeTransmission\File;

use WFSafeTransmission\Codes\SEC\CorporateCreditDebit;
use WFSafeTransmission\Codes\ServiceClassCodes\CreditsOnly;
use WFSafeTransmission\Interfaces\SECCode;

class BatchHeader extends ACHRecord {
	
	protected static $RecordType = '5';

	protected $serviceCode;
	protected $companyName;
	protected $companyId;
	protected $companyDescription;
	protected $discretionaryData;	
	protected $secCode;
	protected $batchNumber;

	public function getId() {
		return $this->batchNumber;
	}

	public static function create($discretionary_data, $entry_description, SECCode $secCode)
    {
        $header = new static;
        $header->setCompanyDescriptiveDate(date('M y'))
        ->setServiceClassCode( new CreditsOnly() )
        ->setSECCode( new CorporateCreditDebit() )
        ->setDiscretionaryData('NEXOGY')
        ->setCompanyEntryDescription('COMMISSIONS');
        return $header;
    }

	public function setDefaults() {
		// Record Type.
		$this->addField(new ACHRecordField(1, static::$RecordType));
		// Settlement Date.
		$this->addField(new ACHRecordField([76,78]));
		// Originator status code.
		$this->addField(new ACHRecordField(79, self::ORIGINATOR_STATUS_CODE));
		// Routing Number.
		$this->addField(new ACHRecordField([80,87], self::WELLS_FARGO_RT_NUMBER));
		// sets company Id.
		$this->setCompanyId( self::COMPANY_ID );
		// Sets the company name.
		$this->setCompanyName( self::COMPANY_NAME );
		// set the effective entry date to current unless overwritten.
		$this->setEffectiveEntryDate( new DateTime );
	}

	/**
	 * Set service class code. 200; 220 (credits only); or 225 (debits only)
	 * @param [type] $code [description]
	 */
	public function setServiceClassCode( ServiceClassCode $code ) {

		$this->serviceCode = $code;
		$this->addField( new ACHRecordField([2,4], $code->getCode() ));

		return $this;
	}

	/**
	 * Returns the ServiceCode that has been assigned to the batch header
	 * @return [type] [description]
	 */
	public function getServiceClassCode(){
		return $this->serviceCode->getCode();
	}

	/**
	 * Set Company name
	 * @param string $name Alphanumeric company name
	 */
	public function setCompanyName($name) {
		$this->companyName = $name;
		$field = new ACHRecordField([5,20], $this->companyName);
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Add optional discretionaty data
	 * @param [type] $data [description]
	 */
	public function setDiscretionaryData( $data ) {
		$this->discretionaryData = $data;
		$field = new ACHRecordField([21,40], $this->discretionaryData);
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Wells Fargo assigned ID.
	 */
	public function setCompanyId( $id ) {
		$this->companyId = $id;
		$this->addField( new ACHRecordField([41,50], $this->companyId));

		return $this;
	}

	public function getCompanyId(){
		return $this->companyId;
	}

	/**
	 * Add SEC code, defined in SECCodes folder.
	 * @param ACHCode $code [description]
	 */
	public function setSECCode(SECCode $code) {
		$this->secCode = $code;
		$this->addField(new ACHRecordField([51,53], $this->secCode->getCode()));

		return $this;
	}

	/**
	 * Company entry description
	 * @param string $desc Alphanumeric
	 */
	public function setCompanyEntryDescription( $desc) {
		$this->companyDescription = $desc;
		$field = new ACHRecordField([54, 63], $this->companyDescription);
		$field->addPadding(' ', STR_PAD_RIGHT);
		$this->addField( $field );

		return $this;
	}

	/**
	 * Company descriptive date
	 * @param string $date descriptive
	 */
	public function setCompanyDescriptiveDate( $date = null) {
		$this->date = $date;
		$this->addField( new ACHRecordField([64,69], $date ));

		return $this;
	}

	public function setBatchNumber( $number ){
		$field = new ACHRecordField([88,94], strval($number) );
		$field->addPadding('0');
		$this->addField($field);
		$this->batchNumber = $field->getData();
		return $this;
	}

	/**
	 * Set Effective Entry Date
	 * @param [type] $date [description]
	 */
	public function setEffectiveEntryDate(DateTime $date ) {
		$this->effectiveDate = $date;
		$this->addField( new ACHRecordField([70,75], $date->format('ymd')));
	}


	public function validate() {
		if( empty($this->batchNumber) )
			// throw new Exception('Batch number is required.');

		if( empty($this->companyName) )
			throw new Exception('Company name is required in BatchHeader.');

		if( empty($this->companyId) )
			throw new Exception('Company id is required in BatchHeader.');

		if( empty($this->companyDescription))
			throw new Exception('Company Entry Description is required in BatchHeader.');
			
		if( empty($this->serviceCode) )
			throw new Exception('Service Class Code is required in BatchHeader.');

		if( empty($this->secCode) )
			throw new Exception('SEC code is required in BatchHeader.');
	}
}