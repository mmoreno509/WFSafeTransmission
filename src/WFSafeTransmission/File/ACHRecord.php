<?php
namespace WFSafeTransmission\File;

use \Exception;
use WFSafeTransmission\Interfaces\RecordInterface;

abstract class ACHRecord implements RecordInterface {

	// Record Constants
	const PRIORITY_CODE 				= '01';
	const BLOCKING_FACTOR 				= '10';
	const FORMAT_CODE					= '1';
	const ORIGINATION_BANK 				= 'WELLS FARGO';
	const FILE_ID						= '5650931655';
	const COMPANY_ID					= '5650931655';
	const RECORD_SIZE					= '094';
	const ORIGINATOR_STATUS_CODE		= '1';
	const WELLS_FARGO_RT_NUMBER			= '09100001';
	const WELLS_FARGO_FILE_RT_NUMBER	= '091000019';
	const COMPANY_NAME					= 'LD TELECOM';

	/**
	 * custom unique identifier for the object
	 * @var [type]
	 */
	protected $id;

	/**
	 * Final string data for record data
	 * @var [type]
	 */
	protected $data;

	/**
	 * Fields to be added to the Record
	 * @var array
	 */
	protected $fields = array();

	public function __construct() {
		$this->id = strval(rand());

		if ( !isset(static::$RecordType))
            throw new Exception('Class '.get_called_class().' failed to define static "Record Type" property');        

		// create blank record for writing.
        $this->data = str_repeat(' ', self::RECORD_SIZE);

        // prepoluate with defaults
        $this->setDefaults();
	}

	/**
	 * Adds a fields to the record
	 * @param ACHRecordField $field [description]
	 */
	protected function addField(ACHRecordField $field ){
		$this->fields[ $field->getStartPosition() ] = $field;

		return $this;
	}

	public function getId(){
		return $this->id;
	}

	public static function formatDecimal($number) {
		// removes non-negative values and sets a calculable amount for the class to use.
		$number = (float) abs($number);
		
		// explode to separate dollars from decimals.
		$number = explode('.',strval($number));
		
		if(! isset($number[1]))
			return $number[0] . '00';
		
		// inserts trailing zeros on decimal if missing. (gets removed on floats like .50)
		if( strlen($number[1]) < 2 )
			$number[1] .= '0';

		// rebuild the total number
		return $number[0] . $number[1];
	}

	/**
	 * Returns the Record data limited to its defined record size
	 * 
	 * @return string The record information as a single line string of defined length.
	 */
	public final function getData() {

		foreach ($this->fields as $field) {
			$this->write( $field );
		}

		// reduce set to maximum allowed record length when returning data.
		$this->data = substr($this->data, 0, self::RECORD_SIZE );

		// validation method
		$this->validate();

		// returns the prepared record with new line return character;
		return $this->data . "\n";
	}

	/**
	 * Writes the given integer as a right aligned with leading 0's
	 * to the given record field object.
	 *
	 * @param  ACHRecordField $field The Field data to write
	 * @return void
	 */
	private final function write(ACHRecordField $field) {
		$this->data = substr_replace(
            $this->data, 				// current data
            $field->getData(), 			// data to insert
            $field->getStartPosition(), // position to insert,
            $field->length() 			// length of string to insert
        );
	}

}