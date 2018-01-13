<?php
namespace WFSafeTransmission\RecordTypes;

use WFSafeTransmission\File\ACHBatch;
use WFSafeTransmission\File\ACHRecord;
use WFSafeTransmission\File\ACHRecordField;

class BatchControl extends ACHRecord {
	
	protected static $RecordType = '8';

	protected $hash;

	public function __construct( ACHBatch $batch ) {
		parent::__construct();
		$this->setControlData($batch);
	}

	/**
	 * Set default and constant field values for the record
	 */
	public function setDefaults() {
		// Add record type
		$this->addField( new ACHRecordField(1,static::$RecordType));

		//Entry Hash, leave blank, ACH System corrects
		$hash = new ACHRecordField([11,20], '0');
		$hash->addPadding('0');
		$this->addField( $hash );

		// Message Authentication Code
		$this->addField( new ACHRecordField([55,73]));

		// Blank Spaces
		$this->addField( new ACHRecordField([74,79]));

		// Wells fargo routing number
		$this->addField( new ACHRecordField([80, 87], self::WELLS_FARGO_RT_NUMBER));
	}

	public function setEntryHash( $hash ) {
		$this->hash = $hash;
		// field data
		$field = new ACHRecordField([11,20], $this->hash);
		// add '0' padding
		$field->addPadding('0');
		// insert field
		$this->addField( $field );
	}

	public function setControlData(ACHBatch $batch) {
		// Make sure header is valid
		$batch->getHeader()->validate();
		
		// Service field from header
		$this->addField( new ACHRecordField([2,4], $batch->getHeader()->getServiceClassCode() ));

		// Set Entry Addenda Count
		$this->setEntryAddendaCount( $batch->getEntryCount() );

		// Set Total Debit Dollar Amount
		$totalDebit = new ACHRecordField([21,32], static::formatDecimal($batch->getTotalDebit()) );
		$totalDebit->addPadding('0');
		$this->addField( $totalDebit );

		// Set total Credit Dollar Amount
		$totalCredit = new ACHRecordField([33,44], static::formatDecimal($batch->getTotalCredit()) );
		$totalCredit->addPadding('0');
		$this->addField( $totalCredit );

		// Company ID from header
		$this->addField( new ACHRecordField([45,54], $batch->getHeader()->getCompanyId() ));

		// Set batch Number from header
		$this->addField( new ACHRecordField([88,94], $batch->getHeader()->getId() ));
	}

	private function setEntryAddendaCount( $count ) {
		// Number is padded to the left
		$field = new ACHRecordField([5,10], $count);
		$field->addPadding('0');
		$this->addField( $field );
	}

	public function validate() {

	}
}