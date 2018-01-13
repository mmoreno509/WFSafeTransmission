<?php

/**
 * Takes a given CPCommissionReportGenerator instance and produces the ach batch for the report.
 * Also assures that if there is already a report based on the given time frame then it simply
 * updates the report data.
 * 
 */

class ACHPayoutGenerator {

	/**
	 * The report the payout will be based on.
	 * 
	 * @var [type]
	 */
	protected $report;

	/**
	 * The Payout final ACH batch file.
	 * @var [type]
	 */
	protected $file;

	/**
	 * The current batch to which details will be added to.
	 * @var [type]
	 */
	protected $batch;

	// Opens a new ACHFile object for writing.
	public function __construct(CPCommissionReportGenerator $report ){
		if($report->getData()->count() < 1)
            throw new Exception("No data available to generate payout.");
		$this->report = $report;
		$this->openFile();
	}

	/**
	 * Uses the data provided by CPCommissionReportGenerator
	 * and creates an ACHFile.
	 * 
	 * @return [type] [description]
	 */
	public function generate() {

		$this->openBatch();
			
		foreach($this->report->getData() as $cp){
			if( is_null($cp->user->bankaccount) )
				throw new Exception("Account missing for user {$cp->user_id}");

            if($cp->user->bankaccount->account_type == BankAccount::CHECKING){
                $transaction_code =  new CheckingCredit;
            } else {
                $transaction_code =  new SavingsCredit;
            }

			$entry = new EntryDetail();
			$entry->setIndividualName( $cp->user->bankaccount->account_name )
				->setIndividualId( $cp->name )
				->setRecievingRTNumber( $cp->user->bankaccount->routing_number )
				->setRecievingAccountNumber( $cp->user->bankaccount->account_number )
                ->setTransactionCode($transaction_code)
				->setAmount( $cp->total_commission_paid);

            $this->batch->addEntryDetail( $entry );


            $cp->outstandingCommissionReports()->each(function($report) use($cp,$transaction_code) {
                $entry = new EntryDetail();
                $entry->setIndividualName( $cp->user->bankaccount->account_name )
                    ->setIndividualId( $cp->name )
                    ->setRecievingRTNumber( $cp->user->bankaccount->routing_number )
                    ->setRecievingAccountNumber( $cp->user->bankaccount->account_number )
                    ->setTransactionCode($transaction_code)
                    ->setAmount($report->total_commission_paid) // amount comes from outstanding report this time...
                    ->addAddenda(new Addenda('*outstanding-balance-'.$report->report_year.'-'.$report->report_month . '*'));

                $this->batch->addEntryDetail($entry);
            });
		}

		$this->file->addBatch( $this->batch );
		$this->file->close();

		// store in the database.
		$this->recordBatchFile();

		return $this->file;
	}

	public function openFile() {
		$this->file = new ACHFile();
		$this->file->setFilePath(storage_path('ach-batches/'));
	}

	public function openBatch(){
		$batchHeader = new BatchHeader();
		$batchHeader
			->setCompanyDescriptiveDate(date('M y'))
			->setServiceClassCode( new CreditsOnly )
			->setSECCode( new CorporateCreditDebit )
			->setDiscretionaryData('NEXOGY')
			->setCompanyEntryDescription('COMMISSIONS');
		$this->batch = new ACHBatch($batchHeader);
	}

	/**
	 * Writes batch file data to the database as well as
	 * records all payout entries for the batch file.
	 * 
	 * @return [type] [description]
	 */
	private function recordBatchFile() {

		// find or create a new batch file database record. 
		// Making sure there is only 1 for every month
		$payout = ACHPayout::firstOrNew(array(
			'month' => $this->report->getFilter()->getStartDate()->format('n'),
			'year' 	=> $this->report->getFilter()->getStartDate()->format('Y'),
			));

		// Save it only if it doesnt exist.
		if( !$payout->exists ){
			$payout->status = ACHStatus::PENDING;
			$payout->save();
		}

		// Save the generated batch file to AWS S3.
		$doc = Document::store($this->file->write(), $this->file->getFileName(), 'ach', null, Document::PRIVATE_ACCESS);
		
		// create the batch file record
		$batchFile = ACHBatchFile::create(array(
			'file_name' => $this->file->getFileName(),
			'total_debits' => $this->file->getTotalDebit(),
			'total_credits' => $this->file->getTotalCredit(),
			));

		// Associate the document record
		$batchFile->document()->associate( $doc );

		// Create entries for the batchfile.
		$entries = array();
		foreach($this->report->getData() as $cp_payout){
		    // ensures single payout entry per commission_report_id (allows only one payment of each report)
		    $entry = ACHPayoutEntry::firstOrNew(['commission_report_id' => $cp_payout->report_id]);
            $entry->fill(array(
				'name' => $cp_payout->name,
				'routing' => $cp_payout->user->bankaccount->routing_number,
				'account' => $cp_payout->user->bankaccount->account_number, 
				'total_credit' => $cp_payout->total_commission_paid,
				));
            $entries[] = $entry;

            $cp_payout->outstandingCommissionReports()->each(function($report) use($cp_payout, &$entries) {
                // ensures single payout entry per commission_report_id (allows only one payment of each report)
                $entry = ACHPayoutEntry::firstOrNew(['commission_report_id' => $report->id]);
                $entry->fill(array(
                    'name' => $cp_payout->name,
                    'routing' => $cp_payout->user->bankaccount->routing_number,
                    'account' => $cp_payout->user->bankaccount->account_number,
                    'total_credit' => $report->total_commission_paid,
                ));
                $entries[] = $entry;
            });
		}
		$batchFile->entries()->saveMany( $entries );
		
		// Associate the batch file to the payout
		$payout->batchFiles()->save( $batchFile );

	}	

	/**
	 * Creates an EntryDetail object from the bank account object
	 * and the given amount.
	 * 
	 * @param  BankAccount $bank   [description]
	 * @param  [type]      $amount [description]
	 * @return [type]              [description]
	 */
	public function createEntry(BankAccount $bank , $amount) {
		$entry = new EntryDetail();

		// Setting the account numbers.
		$entry->setRecievingRTNumber( $bank->routing_number);
		$entry->setRecievingAccountNumber( $bank->account_number );

		// The transaction type based on the amount (pos is credit, neg is debit.)
		if($amount > 0){
			$entry->setTransactionCode( new CheckingCredit );
		} else {
			$entry->setTransactionCode( new CheckingDebit );
		}

		// Set the entry amount
		$entry->setAmount( $amount );

		// return the EntryDetail Object
		return $entry;
	}
}