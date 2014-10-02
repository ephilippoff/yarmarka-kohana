<?php defined('SYSPATH') OR die('No direct script access.');

class Spreadsheet extends Kohana_Spreadsheet {

/**
	 * Writes cells to the spreadsheet
	 *  array(
	 *	   1 => array('A1', 'B1', 'C1', 'D1', 'E1'),
	 *	   2 => array('A2', 'B2', 'C2', 'D2', 'E2'),
	 *	   3 => array('A3', 'B3', 'C3', 'D3', 'E3'),
	 *  );
	 * 
	 * @param array of array( [row] => array([col]=>[value]) ) ie $arr[row][col] => value
	 * @return void
	 */
	public function set_data(array $data, $multi_sheet = FALSE)
	{
		// Single sheet ones can just dump everything to the current sheet
		if ( ! $multi_sheet)
		{
			$sheet = $this->_spreadsheet->getActiveSheet();
			$this->set_sheet_data($data, $sheet);
		}
		// Have to do a little more work with multi-sheet
		else
		{
			foreach ($data as $sheetname => $sheetData)
			{
				$sheet = $this->_spreadsheet->createSheet();
				$sheet->setTitle($sheetname);
				$this->set_sheet_data($sheetData, $sheet);
			}
			// Now remove the auto-created blank sheet at start of XLS
			$this->_spreadsheet->removeSheetByIndex(0);
		}
	}

	protected function set_sheet_data(array $data, PHPExcel_Worksheet $sheet)
	{
		foreach ($data as $row => $columns)
		{
			foreach ($columns as $column => $value)
			{
				$sheet->setCellValueByColumnAndRow($column, $row, $value);
			}
		}
	}

}