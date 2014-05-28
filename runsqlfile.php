<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
/** MySQL query pull and return data class */
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

/** Object::runsqlfile
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class runsqlfile {
	//: Variables

	//: Public functions
	//: Accessors

	/** runsqlfile::writeExcelFile($excelFile, $excelData)
	 *  Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
	 *  @param $excelFile, $excelData
	 */
	public function writeExcelFile($excelFile, $excelData) {
		// Create new PHPExcel object
		print("<pre>");
		print(date('H:i:s') . " Create new PHPExcel object" . PHP_EOL);
		$objPHPExcel = new PHPExcel();
		//: End

		//: Set properties
		print(date('H:i:s') . " Set properties" . PHP_EOL);
		$objPHPExcel->getProperties()->setCreator("Clinton Wright");
		$objPHPExcel->getProperties()->setLastModifiedBy("Clinton Wright");
		$objPHPExcel->getProperties()->setTitle("title");
		$objPHPExcel->getProperties()->setSubject("subject");
		$objPHPExcel->getProperties()->setDescription("description");
		//: End

		//: Setup Workbook Preferences
		print(date('H:i:s') . " Setup workbook preferences" . PHP_EOL);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		//: End

		//: Set Column Headers
		$alphaA = range('A', 'Z'); $alphaVar = range('A', 'Z');
		foreach($alphaA as $valueA) {
			foreach($alphaA as $valueB) {
				$alphaVar[] = $valueA . $valueB;
			}
		}
		print(date('H:i:s') . " Setup column headers" . PHP_EOL);
		$a = 0;
		foreach($excelData[0] as $key => $value) {
			$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$a] . "1")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$a] . "1", $key);
			$a++;			
		}

		//Add more column header value assignments here
		//: End

		//: Add data from $excelData array
		print(date('H:i:s') . " Add data from [reportName] report" . PHP_EOL);
		$rowCount = (int)2; $colCount = (int)0;
		$objPHPExcel->setActiveSheetIndex(0);
		foreach($excelData as $value1) {
			foreach($value1 as $key2 => $value2) {
				$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . strval($rowCount))->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
				$colCount++;
			}
			$colCount = 0;
			$rowCount++;
		}
		//: End

		//: Setup Column Widths
		$a = 0;
		foreach($excelData[0] as $key => $value) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$a])->setAutoSize(true);
			$a++;
		}
		// Add more column widths here
		//: End

		//: Rename sheet
		//print(date('H:i:s') . " Rename sheet" . PHP_EOL);
		//$objPHPExcel->getActiveSheet()->setTitle(date('Y-m', strtotime('-1 month')));
		//: End

		//: Save spreadsheet to Excel 2007 file format
		print(date('H:i:s') . " Write to Excel2007 format" . PHP_EOL);
		print("</pre>" . PHP_EOL);
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($excelFile);
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unset($objWriter);
		//: End
	}

	//: Magic
	/** runsqlfile::__construct()
	* Class constructor
	*/
	public function __construct() {
	// Construct an array with predefined date(s) which we will use to run a report
		$options = getopt("f:");
		$sqlfile = $options["f"];
		echo "SQL File: " . $sqlfile . ".sql" . PHP_EOL;
		$sqlData = new PullDataFromMySQLQuery();
		$_data = $sqlData->getDataFromSQLFile($sqlfile . ".sql", "", "", FALSE);
		if ($_data == FALSE) {
			print_r($sqlData->getErrors());
			die;
		}
		$this->writeExcelFile(dirname(__FILE__) . DIRECTORY_SEPARATOR .  $sqlfile . ".xlsx", $_data);
	}

	/** runsqlfile::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
} new runsqlfile();