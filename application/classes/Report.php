<?php defined('SYSPATH') OR die('No direct script access.');

class Report {

	private $report_type;
	private $titles = array();
	private $data = array();

	public function __construct($report_type) {
		$this->report_type = strtolower($report_type);
	}

	public function setTitles($title = array()) {
		if (!is_array($title)) {
			throw new Kohana_Exception('Titles value must be an array');
		}

		$result = array_merge($this->titles, $title);

		$this->titles = $result;

		return $this;
	}

	public function setData($data = array()) {
		if (!is_array($data)) {
			throw new Kohana_Exception('Report data must be an array');
		}

		$result = array_merge($this->data, $data);

		$this->data = $result;

		return $this;
	}



	public function save() {
		switch ($this->report_type) {
			case 'excel':
				$this->saveExcelReport();
				break;
			
			default:
				throw new Kohana_Exception("Error Processing Request", 1);
				break;
		}
	}

	public function saveExcelReport() {

		$report = Spreadsheet::factory(array(
	      	'author'  => 'Kohana-PHPExcel',
	      	'title'      => 'Report',
	      	'subject' => 'Subject',
	      	'description'  => 'Description',
	      	'path' => 'uploads/reports/',
	      	'name' => 'report'
		));
		$report->set_active_worksheet(0);
		$as = $report->get_active_worksheet();

		$endCellOfTitles = chr(ord('A') + count($this->titles) - 1);

		$reportData[1] = $this->titles;

		$dataLength = count($this->data);

		for ($i = 0; $i < $dataLength; $i++) { 
			$reportData[$i+2] = $this->data[$i];
		}

		$report->set_data($reportData, false);

		$report->save();
	}

}