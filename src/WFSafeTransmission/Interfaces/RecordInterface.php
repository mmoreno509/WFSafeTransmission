<?php
namespace WFSafeTransmission\Interfaces;

interface RecordInterface {

	/**
	 * Validates the Record Type Data
	 * @return [type] [description]
	 */
	public function validate();

	/**
	 * Returns a unique identifier for the record data.
	 * @return [type] [description]
	 */
	public function getId();

	/**
	 * Sets default and constant fields in the inheriting class;
	 */
	public function setDefaults();
	
	/**
	 * Returns the record data as a string.
	 * @return [type] [description]
	 */
	public function getData();

}