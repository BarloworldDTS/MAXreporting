<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::VarianceReport
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class VarianceReport {
	//: Constants
	const REPORT_NUMBER = 134;
	const REPORT_NAME = "Variance Report";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "Variance Report";
	const DOC_FILE_NAME = "VarianceReport";
	const DOC_SUBJECT = "Exported MAX Report of Variance Report using API calls";

	//: Variables
	protected $_apiurl	= "https://login.max.manline.co.za/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;
	protected $_numberFields = array("Manline Delivery Note Number");
	protected $_currencyFields = array("Rate (R)", "Amount");


	//: Public functions
	//: Accessors
	/** VarianceReport::getApiUrl()
	*   base url to call
	*   @return string
	*/
	protected function getApiUrl() {
		return $this->_apiurl;
	}

	public function reportMemory($_reportTitle) {
		print("<pre>");
		print($_reportTitle . PHP_EOL);
		print_r(date("H:i:s") . " Memory used total: " . strval(intval(memory_get_usage()) / 1000000). "MB" . PHP_EOL);
		print("</pre>");
	}
	
	/** VarianceReport::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}
	
	/** VarianceReport::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}
	
	/** VarianceReport::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}
	
	/** VarianceReport::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** VarianceReport::writeExcelFile($excelFile, $excelData)
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
		$objPHPExcel->getProperties()->setTitle("Monthly Variance Report Export ". date('Y_m', strtotime('-1 month')));
		$objPHPExcel->getProperties()->setSubject("Monthly Variance Report Export");
		$objPHPExcel->getProperties()->setDescription("Variance report exported and generated using PHP.");
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
		print(date('H:i:s') . " Setup column headers" . PHP_EOL);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('O1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('Q1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('R1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue("A1", "Trip No");
		$objPHPExcel->getActiveSheet()->setCellValue("B1", "Invoice No.");
		$objPHPExcel->getActiveSheet()->setCellValue("C1", "Loading Started");
		$objPHPExcel->getActiveSheet()->setCellValue("D1", "Customer");
		$objPHPExcel->getActiveSheet()->setCellValue("E1", "City From");
		$objPHPExcel->getActiveSheet()->setCellValue("F1", "City To");
		$objPHPExcel->getActiveSheet()->setCellValue("G1", "Actual Trip Days");
		$objPHPExcel->getActiveSheet()->setCellValue("H1", "Expected Trip Days");
		$objPHPExcel->getActiveSheet()->setCellValue("I1", "Trip Days");
		$objPHPExcel->getActiveSheet()->setCellValue("J1", "Actual Trip Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("K1", "Expected Trip Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("L1", "Trip Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("M1", "Actual Trip Empty Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("N1", "Expected Trip Empty Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("O1", "Trip Empty Kms");
		$objPHPExcel->getActiveSheet()->setCellValue("P1", "Income");
		$objPHPExcel->getActiveSheet()->setCellValue("Q1", "Contribution");
		$objPHPExcel->getActiveSheet()->setCellValue("R1", "Weighted Average Contribution (Out)");
		//: End

		//: Add data from $excelData array
		print(date('H:i:s') . " Add data from Variance Report" . PHP_EOL);
		$rowCount = (int)2;
		$objPHPExcel->setActiveSheetIndex(0);
		foreach ($excelData as $val_a) {
			foreach ($val_a as $val_b) {
				$objPHPExcel->getActiveSheet()->getCell('A' . strval($rowCount))->setValueExplicit($val_b["Trip No"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('B' . strval($rowCount))->setValueExplicit($val_b["Invoice No."], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('C' . strval($rowCount))->setValueExplicit($val_b["Loading Started"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('D' . strval($rowCount))->setValueExplicit($val_b["Customer"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('E' . strval($rowCount))->setValueExplicit($val_b["City From"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('F' . strval($rowCount))->setValueExplicit($val_b["City To"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('G' . strval($rowCount))->setValueExplicit($val_b["Actual Trip Days"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->getCell('H' . strval($rowCount))->setValueExplicit($val_b["Expected Trip Days"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->setCellValue("I" . strval($rowCount), "=SUM(G" . strval($rowCount) . "-H" . strval($rowCount) . ")");
				$objPHPExcel->getActiveSheet()->getCell('J' . strval($rowCount))->setValueExplicit($val_b["Actual Trip Kms"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->getCell('K' . strval($rowCount))->setValueExplicit($val_b["Expected Trip Kms"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->setCellValue("L" . strval($rowCount), "=SUM(J" . strval($rowCount) . "-K" . strval($rowCount) . ")");
				$objPHPExcel->getActiveSheet()->getCell('M' . strval($rowCount))->setValueExplicit($val_b["Actual Trip Empty Kms"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->getCell('N' . strval($rowCount))->setValueExplicit($val_b["Expected Trip Empty Kms"], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->getActiveSheet()->setCellValue("O" . strval($rowCount), "=SUM(M" . strval($rowCount) . "-N" . strval($rowCount) . ")");
				$objPHPExcel->getActiveSheet()->getCell('P' . strval($rowCount))->setValueExplicit($val_b["Income"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('Q' . strval($rowCount))->setValueExplicit($val_b["Contribution"], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell('R' . strval($rowCount))->setValueExplicit($val_b["Weighted Average Contribution (Out)"], PHPExcel_Cell_DataType::TYPE_STRING);
				$rowCount++;
			}
		}
		//: End

		//: Setup Column Widths
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
		//: End

		//: Rename sheet
		print(date('H:i:s') . " Rename sheet" . PHP_EOL);
		$objPHPExcel->getActiveSheet()->setTitle(date('Y-m', strtotime('-1 month')));
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
	/** VarianceReport::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			//: Construct a multidimensional array containing the year => month => days
			$constructedDates = (array) array();
			// Build an array of the desired date range
			$reportDates = (array) $this->createDateRangeArray(date("Y-m-01", strtotime("-1 month")), date("Y-m-01"));
			$firstYear = intval(substr($reportDates[0], 0, 4)); $lastYear = intval(substr($reportDates[(count($reportDates) - 1)], 0, 4));
			// Determine the first and last month and the total number of months
			$firstMonth = (string) substr($reportDates[0], -5, 2); $lastMonth = (string) substr($reportDates[(count($reportDates) - 1)], -5, 2);
			// Construct initial parts of our mulitdimensional array
			$aYear = $firstYear; $constructedDates[$firstYear] = array(); $aMonth = $firstMonth; $constructedDates[$firstYear][$firstMonth] = array();
			// Construct the multidimensional array with the dates
			foreach ($reportDates as $value) {
				if ($aYear != substr($value, 0, 4)) {
					$constructedDates[substr($value, 0, 4)] = array();
					$aYear = substr($value, 0, 4);
				}
				if ($aMonth != substr($value, -5, 2)) {
					$constructedDates[substr($value, 0, 4)][substr($value, -5, 2)] = array();
					$aMonth = substr($value, -5, 2);
				} 
				$constructedDates[$aYear][$aMonth][] = $value;
			}
			print_r($constructedDates);
			//: End
			
			// Set variables
			$all = (array)array(); $dataCount = (int) 0;
				
			foreach ($constructedDates as $key1 => $value1) {
				foreach ($value1 as $key2 => $value2) {
					$all = array();
					$this->setFileName(self::DOC_FILE_NAME .date("-" . $key1 . "-" . $key2));
					$this->setExcelFile("/export/" . $this->getFileName());
					print_r($key1 . "-" . $key2 . PHP_EOL); // print the year and month
					foreach($value2 as $key3 => $value3) {
						$aDate = strtotime("+1 day", strtotime($value3));
						$reportData = array(($value3 . " 00:00:00"), (date("Y-m-d", $aDate)) . " 00:00:00");
						$reportUrl = $this->getApiUrl()."report=" . strval(self::REPORT_NUMBER) . "&responseFormat=csv&Start_Date=" . $reportData[0] . "&Stop_Date=" . $reportData[1];
						// Comply with HTML string standard for whitespace characters and replace with %20
						$reportUrl = str_replace(" ", "%20", $reportUrl);
						$fileParser = new FileParser($reportUrl);
						$fileParser->setCurlFile($this->getFileName() . "-" . $value3 . ".csv");
						$data = $fileParser->parseFile();
						$dataCount = count($data); 
						if ($dataCount != 0) {
							$all[] = $data;
						}
						echo "."; // print and period to indicate a report is successfully completed
					}
					echo "\n";
					$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFile() . ".xlsx", $all, self::REPORT_NAME . "-" . $key1 . "-" . $key2, $key1 . "-" . $key2);
				}
			}
		} catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			echo "F", "\n";
			exit;
		}
	}

	/** VarianceReport::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
	//: Private Functions
	
	/** createDateRangeArray($strDateFrom,$strDateTo)
	 * @see http://stackoverflow.com/questions/4312439/php-return-all-dates-between-two-dates-in-an-array
	 * @param unknown $strDateFrom
	 * @param unknown $strDateTo
	 * @return multitype:
	 */
	private function createDateRangeArray($strDateFrom,$strDateTo) {
		// takes two dates formatted as YYYY-MM-DD and creates an
		// inclusive array of the dates between the from and to dates.
	
		// could test validity of dates here but I'm already doing
		// that in the main script
	
		$aryRange=array();
	
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
	
		if ($iDateTo>=$iDateFrom) {
			array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo) {
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y-m-d',$iDateFrom));
			}
		}
		return $aryRange;
	}
	//: End
}

new VarianceReport();