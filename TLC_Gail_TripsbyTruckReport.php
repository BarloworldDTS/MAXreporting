<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
//ini_set("memory_limit", "64M");
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::TLC_Gail_TripsbyTruckReport
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class TLC_Gail_TripsbyTruckReport {
	//: Constants
	const REPORT_NUMBER = 22;
	const REPORT_NAME = "Trips by Truck";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "TLC Global - Gail: Trips by Truck Report";
	const DOC_FILE_NAME = "TripsbyTruck";
	const DOC_SUBJECT = "Exported MAX Report of Trips by Truck";
	const BUNIT = "1,4";

	//: Variables
	protected $_apiurl	= "https://login.max.bwtsgroup.com/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;
	protected $_numberFields = array("Cargo", "Tripleg ID");

	//: Public functions
	//: Accessors
	/** TLC_Gail_TripsbyTruckReport::getApiUrl()
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

	/** TLC_Gail_TripsbyTruckReport::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}

	/** TLC_Gail_TripsbyTruckReport::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}

	/** TLC_Gail_TripsbyTruckReport::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}

	/** TLC_Gail_TripsbyTruckReport::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** TLC_Gail_TripsbyTruckReport::writeExcelFile($excelFile, $excelData)
	 *  Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
	 *  @param $excelFile, $excelData
	 */
	public function writeExcelFile($excelFile, $excelData, $reportName) {
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
				$alphaVar = range('A', 'Z');
				$i = 0;
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
							if (preg_grep("/" . $key . "/", $this->_numberFields)) {
								$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
							} else if ($key == "Income") {
								$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$i] . strval($rowCount))->getNumberFormat()->setFormatCode('[$R-1C09] 0.00;[RED][$R-1C09]-0.00');
								$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit(str_replace(",", "", $value), PHPExcel_Cell_DataType::TYPE_NUMERIC);
							} else {
								$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
							}
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
				$objPHPExcel->getActiveSheet()->setTitle(date('Y-m', strtotime('-1 month')) . " - " . self::REPORT_NAME);
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
	/** TLC_Gail_TripsbyTruckReport::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			// Construct an array with predefined date(s) which we will use to run a report
			$businessUnit = (int) 0;
			$lastDay = intval(date("t", strtotime("-1 month")));
			$previousMonth = intval(date("m", strtotime("-1 month")));
			$currentYear = intval(date("Y"));
			// Set variables
			$all = (array)array();
			$this->setFileName(self::DOC_FILE_NAME .date("-Y-m", strtotime("-1 month")));
			$this->setExcelFile("/export/" . $this->getFileName());
			for($i = 1; $i <= $lastDay; $i++) {
				if ($i == $lastDay) {
					$d = 1; $m = $previousMonth + 1;
				} else {
					$d = ($i + 1); $m = $previousMonth;
				}
				$reportData = array(date("Y-m-" . strval($i) . " 00:00:00", strtotime("-1 month")), date("Y-" . strval($m) . "-" . strval($d) . " 00:00:00"));
				$reportUrl = $this->getApiUrl()."report=" . strval(self::REPORT_NUMBER) . "&responseFormat=csv&Start_Date=" . $reportData[0] . "&Stop_Date=" . $reportData[1] . "&Business_Unit=" . self::BUNIT;
				// Clean out whitespace characters and replace with %20
				$reportUrl = str_replace(" ", "%20", $reportUrl);
				$fileParser = new FileParser($reportUrl);
				$fileParser->setCurlFile($this->getFileName() . ".csv");
				$data = $fileParser->parseFile();
				$all[] = $data;
				print(".");
			}
			print(PHP_EOL);
			$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFile() . ".xlsx", $all, self::REPORT_NAME . date(" Y-m", strtotime("-1 month")));
		}
		catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			exit;
		}
	}

	/** TLC_Gail_TripsbyTruckReport::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
}

new TLC_Gail_TripsbyTruckReport();