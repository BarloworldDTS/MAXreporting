<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::T24_Triplist_by_Fleet
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class T24_Triplist_by_Fleet {
	//: Constants
	const REPORT_NUMBER = 80;
	const REPORT_NAME = "Triplist by Fleet";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "Triplist by Fleet";
	const DOC_FILE_NAME = "T24_Triplist_by_Fleet";
	const DOC_SUBJECT = "Triplist by Fleet MAX Report - Timber 24";

	//: Variables
	protected $_apiurl	= "https://t24.max.bwtsgroup.com/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;

	//: Public functions
	//: Accessors
	/** T24_Triplist_by_Fleet::getApiUrl()
	*   base url to call
	*   @return string
	*/
	protected function getApiUrl() {
		return $this->_apiurl;
	}

	/** T24_Triplist_by_Fleet::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFileName() {
		return $this->_excelFileName;
	}

	/** T24_Triplist_by_Fleet::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFileName($_setFile) {
		$this->_excelFileName = $_setFile;
	}

	/** T24_Triplist_by_Fleet::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}

	/** T24_Triplist_by_Fleet::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** T24_Triplist_by_Fleet::writeExcelFile($excelFile, $excelData)
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
				$objPHPExcel->getProperties()->setTitle(self::DOC_TITLE);
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
	/** T24_Triplist_by_Fleet::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			// Get options supplied from command line
			$options = getopt("s:e:f:n:");

			// : MANDATORY OPTION - Get Start Date from supplied command line options
			if (array_key_exists("s", $options)) {
				$startDate = $options["s"];
			}
			else {
				throw new Exception("Mandatory s option, not provided. Please include the following option when running script from the command line:\n-s $[YYYY-mm-dd]");
			}
			// : End
			
			// : MANDATORY OPTION - Get Start Date from supplied command line options
			if (array_key_exists("e", $options)) {
				$endDate = $options["e"];
			}
			else {
				throw new Exception("Mandatory e option, not provided. Please include the following option when running script from the command line:\n-e $[YYYY-mm-dd]");
			}
			// : End
			
			// : MANDATORY OPTION - Get Fleet ID from supplied command line options
			if (array_key_exists("f", $options)) {
				$fleet = $options["f"];
			} else {
				throw new Exception("Mandatory f option, not provided. Please include the following option when running script from the command line:\n-f $[fleet_id]");
			}
			// : End
			
			//: Set File Name supplied from supplied command line options
			$_csvFileName = (string)"";
			if (array_key_exists("n", $options)) {
				$_csvFileName = $options["n"];
			} else {
				$_csvFileName = self::DOC_FILE_NAME;
			}
			// : End
			
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
					$this->setFileName($_csvFileName .date("-" . $key1 . "-" . $key2));
					$this->setExcelFileName("/export/" . $_csvFileName . "_" . date("Y-m-d"));
					
					// print the year and month
					echo $key1 . "-" . $key2 . PHP_EOL;
					
					foreach($value2 as $key3 => $value3) {
						$aDate = strtotime("+1 day", strtotime($value3));
						$reportData = array(($value3 . " 00:00:00"), (date("Y-m-d", $aDate)) . " 00:00:00");
						$reportUrl = $this->getApiUrl()."report=" . strval(self::REPORT_NUMBER) . "&responseFormat=csv&Start_Date=" . $reportData[0] . "&Stop_Date=" . $reportData[1] . "&Fleet=" . $fleet;
						// Comply with HTML string standard for whitespace characters and replace with %20
						$reportUrl = str_replace(" ", "%20", $reportUrl);
						$fileParser = new FileParser($reportUrl);
						$fileParser->setCurlFile($this->getFileName() . "-" . $value3 . ".csv");
						$data = $fileParser->parseFile();
						$dataCount = count($data); 
						if ($dataCount != 0) { // If report run is empty skip adding to the array
							$all[] = $data;
						}
					}
					
					if (count($all) != 0) {
						$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFileName() . ".xlsx", $all, self::REPORT_NAME . "-" . $key1 . "-" . $key2, $key1 . "-" . $key2);
					}
				}
			}
		} catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			echo "F", "\n";
			exit;
		}
	}

	/** T24_Triplist_by_Fleet::__destruct()
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

new T24_Triplist_by_Fleet();
