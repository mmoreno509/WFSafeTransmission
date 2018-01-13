<?php
namespace WFSafeTransmission\RecordTypes;

use WFSafeTransmission\File\ACHRecord;
use WFSafeTransmission\File\ACHRecordField;

class Addenda extends ACHRecord {
	protected static $RecordType = '7';

	protected $sequence;
	protected $data;
	protected $entryDetailSequence;

	public function __construct( $data ){
		parent::__construct();
		$this->setData($data);
	}

	public function setDefaults() {
		// Set record type
		$this->addField( new ACHRecordField(1, static::$RecordType));
		// Set Addenda type record code
		$this->addField( new ACHRecordField([2,3], '05'));
	}

	/**
	 * Adds payment related information
	 * @param [type] $data [description]
	 */
	public function setData( $data ) {
		$this->data = $data;
		$this->addField( new ACHRecordField([4,83], $data) );
	}

    /**
     * Sequence number that resets for every entry detail
     * @param $number
     */
	public function setSequenceNumber($number) {
		$this->sequence = $number;
		// create the field
		$field = new ACHRecordField([84,87], $this->sequence);
		
		// add padding to the number
		$field->addPadding('0');

		// add the field.
		$this->addField( $field );
	}

	/**
	 * Sets the parent entry detail sequence number
	 * @param [type] $number [description]
	 */
	public function setEntryDetailSequenceNumber( $number ) {
		$this->entryDetailSequence = $number;
		$this->addField( new ACHRecordField([88,94], $this->entryDetailSequence));
	}

	public function validate() {

	}
}