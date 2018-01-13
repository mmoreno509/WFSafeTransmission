<?php

class FileControl extends ACHRecord {
	protected static $RecordType = '9';

	public function __construct( ACHFile $file) {
		parent::__construct();
		$this->setControlData( $file );
	}

	public function setDefaults(){
		// Add record type field
		$this->addField( new ACHRecordField(1, static::$RecordType) );
		
		// Block Count, leave blank ACH System corrects
		$blockCount = new ACHRecordField([8,13], '0');
		$blockCount->addPadding('0');
		$this->addField( $blockCount );

		// Hash entry Total, leave blank ACH System corrects
		$hashTotal = new ACHRecordField([22,31], '0');
		$hashTotal->addPadding('0');
		$this->addField( $hashTotal );
		
		// Filler
		$this->addField( new ACHRecordField([56,94]));
	}

	/**
	 * Populates control data from an ACH file object.
	 * @param ACHFile $file [description]
	 */
	private function setControlData(ACHFile $file) {

		// Set Batch Count from ACHFile
		$batchCount = new ACHRecordField([2,7], $file->getBatchCount() );
		$batchCount->addPadding('0');
		$this->addField( $batchCount );

		// Total Entry/Addenda Count
		$totalEntries = new ACHRecordField([14,21], $file->getTotalEntries());
		$totalEntries->addPadding('0');
		$this->addField( $totalEntries );

		// Total File Debit Amount
		$totalDebit = new ACHRecordField([32,43], static::formatDecimal($file->getTotalDebit()) );
		$totalDebit->addPadding('0');
		$this->addField( $totalDebit );

		// Total File Credit Amount
		$totalCredit = new ACHRecordField([44,55], static::formatDecimal($file->getTotalCredit()) );
		$totalCredit->addPadding('0');
		$this->addField( $totalCredit );
	}
	
	/**
	 * Set Block Count (Optional)
	 * @param int $count [description]
	 */
	private function setBlockCount( $count = null) {
		$field = new ACHRecordField([8,13], $count);
		$field->addPadding(0);
		$this->addField( $field );
	}
	
	/**
	 * Set Entry Hash Total amount
	 * @param int $total [description]
	 */
	private function setEntryHashTotal( $total ) {
		$field = new ACHRecordField([22,31], $total);
		$field->addPadding('0');
		$this->addField($field);
	}

	

	public function validate() {

	}
}