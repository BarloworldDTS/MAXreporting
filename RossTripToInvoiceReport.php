<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::RossTripsToInvoiceReport
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class RossTripsToInvoiceReport {
	//: Constants
	const REPORT_NUMBER = 68;
	const REPORT_NAME = "Trips to Invoice";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "Trips to Invoice";
	const DOC_FILE_NAME = "RossTripsToInvoiceReport";
	const DOC_SUBJECT = "Exported MAX Report of Trips to Invoice using API calls";

	//: Variables
	protected $_apiurl	= "https://login.max.bwtsgroup.com/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;
	protected $_numberFields = array();
	protected $_currencyFields = array("Amount");


	//: Public functions
	//: Accessors
	/** RossTripsToInvoiceReport::getApiUrl()
	*   base url to call
	*   @return string
	*/
	protected function getApiUrl() {
		return $this->_apiurl;
	}

	/** RossTripsToInvoiceReport::getMonths()
	 * @return array: $this->_months
	 */
	public function getMonths() {
		return $this->_months;
	}
	
	/** RossTripsToInvoiceReport::setMonths(array $months)
	 * @param array $months
	 */
	public function setMonths(array $months) {
		$this->_months = $months;
	}

	public function reportMemory($_reportTitle) {
		print("<pre>");
		print($_reportTitle . PHP_EOL);
		print_r(date("H:i:s") . " Memory used total: " . strval(intval(memory_get_usage()) / 1000000). "MB" . PHP_EOL);
		print("</pre>");
	}
	
	/** RossTripsToInvoiceReport::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}
	
	/** RossTripsToInvoiceReport::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}
	
	/** RossTripsToInvoiceReport::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}
	
	/** RossTripsToInvoiceReport::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** RossTripsToInvoiceReport::writeExcelFile($excelFile, $excelData)
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
							if ($key == "Amount") {
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
	/** RossTripsToInvoiceReport::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			$months = (array)array(
					0=>array(date("Y-m-01 00:00", strtotime("-1 month")), date("Y-m-10 00:00", strtotime("-1 month"))),
					1=>array(date("Y-m-10 00:00", strtotime("-1 month")), date("Y-m-20 00:00", strtotime("-1 month"))),
					2=>array(date("Y-m-20 00:00", strtotime("-1 month")), date("Y-m-01 00:00")),
			);
			$this->setMonths($months);
			unset($months);
			$all = (array)array();
			$this->setFileName(self::DOC_FILE_NAME . "_" . date("Y-m"));
			$this->setExcelFile("/export/" . $this->getFileName());
			foreach ($this->getMonths() as $val) {
				$report = $this->getApiUrl()."report="  . strval(self::REPORT_NUMBER) . "&responseFormat=csv&Start_Date=".$val[0]."&Stop_Date=".$val[1] . "&Business_Unit=1";
				print("<pre>");
				print_r($report);
				print("</pre>".PHP_EOL);
				$report = str_replace(" ", "%20", $report);
				$fileParser = new FileParser($report);
				$fileParser->setCurlFile("Ross_TripToInvoice".date("Ymd", strtotime($val[0])).".csv");
				$data = $fileParser->parseFile();
				$all[] = $data;
			}
			$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFile() . ".xlsx", $all, self::REPORT_NAME, date("Y-m", strtotime("-1 month")));
			//: End
			
		} catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			echo "F", "\n";
			exit;
		}
	}

	/** RossTripsToInvoiceReport::__destruct()
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

new RossTripsToInvoiceReport();