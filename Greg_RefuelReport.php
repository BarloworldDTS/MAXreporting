<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::Greg_RefuelReport
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class Greg_RefuelReport {
	//: Constants
	const REPORT_NUMBER = 24;
	const REPORT_NAME = "Refuel Report";
	const DOC_OWNER = "Clinton Wright";
	const DOC_FILE_NAME = "Greg_RefuelReport";
	const DOC_SUBJECT = "Exported report using API calls to MAX";

	//: Variables
	protected $_apiurl	= "https://login.max.bwtsgroup.com/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;

	//: Public functions
	//: Accessors
	/** Greg_RefuelReport::getApiUrl()
	*   base url to call
	*   @return string
	*/
	protected function getApiUrl() {
		return $this->_apiurl;
	}

	/** Greg_RefuelReport::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}

	/** Greg_RefuelReport::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}

	/** Greg_RefuelReport::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}

	/** Greg_RefuelReport::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** Greg_RefuelReport::writeExcelFile($excelFile, $excelData)
	 *  Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
	 *  @param $excelFile, $excelData
	 */
	public function writeExcelFile($excelFile, $excelData, $reportName, $sheetName) {
		try {
			// Check data validility
			if (count($excelData) <> 0) {
	
				//: Create new PHPExcel object
				print("<pre>");
				print(date('H:i:s') . " Create new PHPExcel object" . PHP_EOL);
				$objPHPExcel = new PHPExcel();
				//: End
	
				//: Set properties
				print(date('H:i:s') . " Set properties" . PHP_EOL);
				$objPHPExcel->getProperties()->setCreator(self::DOC_OWNER);
				$objPHPExcel->getProperties()->setLastModifiedBy(self::DOC_OWNER);
				$objPHPExcel->getProperties()->setTitle(self::REPORT_NAME);
				$objPHPExcel->getProperties()->setSubject(self::DOC_SUBJECT);
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
				$alphaA = range('A', 'Z'); $alphaVar = range('A', 'Z');
				foreach($alphaA as $valueA) {
					foreach($alphaA as $valueB) {
						$alphaVar[] = $valueA . $valueB;
					}
				}
				unset($alphaA); $i = (int)0;
				foreach ($excelData[0][1] as $key => $value) {
					$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$i] . '1')->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$i] . "1", $key);
					$i++;
				}
				//: End
	
				//: Add data from $excelData array
				print(date('H:i:s') . " Add data from " . self::REPORT_NAME ." report" . PHP_EOL);
				$rowCount = (int)2;
				$objPHPExcel->setActiveSheetIndex(0);
				foreach ($excelData as $keys => $values) {
					foreach ($excelData[$keys] as $dataRecords) {
						$i = 0;
						foreach ($dataRecords as $key => $value) {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
							$i++;
						}
						$rowCount++;
					}
				}
				//: End
	
				//: Setup Column Widths
				for ($i = 0; $i <= count($excelData[0][1]); $i++) {
					$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$i])->setAutoSize(true);
				}
				//: End
	
				//: Rename sheet
				print(date('H:i:s') . " Rename sheet" . PHP_EOL);
				$objPHPExcel->getActiveSheet()->setTitle($sheetName . " - " . self::REPORT_NAME);
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
			} else {
				print("<pre>");
				print_r("ERROR: The function was passed an empty array");
				print("</pre>");
				exit;
			}
		} catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			exit;
		}
	}

	//: Magic
	/** Greg_RefuelReport::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			$options = getopt("d:");
			$options = getopt("d:e:");
			$startDate = $options["d"];
			$endDate = $options["e"];
			//: Construct a multidimensional array containing the year => month => days
			$constructedDates = (array) array();
			// Build an array of the desired date range
			$reportDates = (array) $this->createDateRangeArray($startDate, $endDate);
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
						if ($dataCount != 0) { // If report run is empty skip adding to the array
							$all[] = $data;
						}
						echo "."; // print and period to indicate a report is successfully completed
					}
					echo "\n";
					if (count($all) != 0) {
						$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFile() . ".xlsx", $all, self::REPORT_NAME . "-" . $key1 . "-" . $key2, $key1 . "-" . $key2);
					}
				}
			}
		} catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			echo "F", "\n";
			exit;
		}
	}

	/** Greg_RefuelReport::__destruct()
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

new Greg_RefuelReport();