<?php
namespace WFSafeTransmission\File;

class ACHRecordField {
	private $startPosition;
	private $endPosition;
	private $data;

	/**
	 * Creates a Filed data object to be insterted into a Record at its given start and end positions.
	 * Data can be null in the case that filler will be added.
	 * Start and end positions reference those written in the Supplemental Guide, this class takes care
	 * of making the necessary translations to programming indexes.
	 *
	 * @param int         $startPosition [description]
	 * @param int         $endPosition   [description]
	 * @param string|null $data          [description]
	 */
	public function __construct($position, $data = null) {
		// defines position parameters for the data.
		if( is_int($position)){
			$this->startPosition = $this->endPosition = ($position - 1);
		} else {
			$this->startPosition = $position[0] - 1 ; // subtract 1 to account for index factor.
			$this->endPosition   = $position[1] - 1;	
		}
		

		// clips data to the permitted size for the Record Position, defined at construct
		// data to insert, can be null in the case of filler data.
		// parses data to string.
		
		$this->data = strval($data);
		if( is_null($this->data) or strlen($data) == 0 ){
			$this->data = str_repeat(" ", $this->length());
		}
	}

	/**
	 * Insert padding into the remaining size of the field.
	 * @param string $char    the character to insert as padding.
	 * @param const $padding the padding type to add @see str_pad() function.
	 */
	public function addPadding($char, $padding = STR_PAD_LEFT){
		$this->data = str_pad($this->data, $this->length(), strval($char), $padding);
	}

	/**
	 * Start Position getter
	 * @return [type] [description]
	 */
	public function getStartPosition() {
		return $this->startPosition;
	}

	/**
	 * Start position setter
	 * @param int $position [description]
	 */
	public function setStartPosition(int $position) {
		$this->startPosition = $position;
	}

	/**
	 * End position getter
	 * @return [type] [description]
	 */
	public function getEndPosition() {
		return $this->endPosition;
	}

	/**
	 * end position setter
	 * @param  int    $position [description]
	 * @return [type]           [description]
	 */
	public function setEndPosition(int $position) {
		$this->endPosition = $position;
	}

	/**
	 * Returns the length of the field record denoted by 
	 * its starting and ending positions.
	 * @return [type] [description]
	 */
	public function length() {
		return ( $this->endPosition - $this->startPosition ) + 1; //adds 1 to account for removed value from index.
	}

	/**
	 * Data setter.
	 * @param [type] $data [description]
	 */
	public function setData($data = null){
		$this->data = $data;
	}

	/**
	 * Data getter.
	 * @return [type] [description]
	 */
	public function getData() {

		if( strlen($this->data) > $this->length() ){
			Log::info("Truncated data: {$this->data}");
		}

		return substr( $this->data, 0, $this->length() );
	}
}