<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
require_once dirname(__FILE__) . '/FileParser.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
include dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';
//: End

/** Object::ChrisWeeklyTriplistByFleetReport
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class ChrisWeeklyTriplistByFleetReport {
	//: Constants
	const REPORT_NUMBER = 106;
	const REPORT_NAME = "Triplist by Fleet";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "Triplist by Fleet";
	const DOC_FILE_NAME = "ChrisWeeklyTriplistByFleetReport";
	const DOC_SUBJECT = "MAX Triplist by Fleet report export via API and sorted into an array by Fleet and custom formatted and saved into an Excel Spreadsheet";

	//: Variables
	protected $_apiurl	= "https://login.max.bwtsgroup.com/api_request/Report/export?";
	protected $_excelFileName;
	protected $_fileName;
	protected $_numberFields = array("Manline Delivery Note Number");
	protected $_currencyFields = array("Rate (R)", "Amount");
	protected $_options;


	//: Public functions
	//: Accessors
	/** ChrisWeeklyTriplistByFleetReport::getApiUrl()
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
	
	/** ChrisWeeklyTriplistByFleetReport::getExcelFile()
	 *  @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}
	
	/** ChrisWeeklyTriplistByFleetReport::setExcelFile($_setFile)
	 *  @param string: $_setFile
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}
	
	/** ChrisWeeklyTriplistByFleetReport::getFileName()
	 *  @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}
	
	/** ChrisWeeklyTriplistByFleetReport::setFileName($_setFile)
	 *  @param string: $_setFile
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}

	/** ChrisWeeklyTriplistByFleetReport::writeExcelFile($excelFile, $excelData, $reportName)
	 *  Create, Write and Save Excel Spreadsheet from collected data obtained from a report
	 *  NOTE: This is a customized version of this function used to format a spreadsheet with
	 *  multiple worksheets using a very complex multidimensional array.
	 *  @param $excelFile, $excelData, $reportName
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
				//: End
				
				//: Set initial worksheet name
				print(date('H:i:s') . " Set initial worksheet name" . PHP_EOL);
				$objPHPExcel->getActiveSheet()->setTitle('Summary');
				//: End
				
				//: Setup Worksheets in Workbook by Fleet
				$i = 1;
				$sdate = strtotime($this->_options["s"]);
				foreach ($excelData as $key => $value) {
					$wsName = $key;
					// Check fleet name character length and shorten if greater than 25 characters
					if (strlen($wsName) > 25) {
						$wsName = substr($wsName, 0, 25);
					}
					$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $wsName . ' (' . date('M', $sdate) . ')');
					$objPHPExcel->addSheet($myWorkSheet, $i);
					$i++;		
				}
				//: End
				
				//: Select each sheet and populate with data
				foreach($excelData as $key1 => $value1) {
					$wsName = $key1;
					// Check fleet name character length and shorten if greater than 25 characters
					if (strlen($wsName) > 25) {
						$wsName = substr($wsName, 0, 25);
					}
					$objPHPExcel->setActiveSheetIndexByName($wsName . ' (' . date('M', $sdate) . ')'); // Set Active Worksheet
				
					//: Setup Page
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
					unset($alphaA); $i = (int)0; $r = 0; $k = (string)'';
					foreach($value1["Data"] as $keyRecord => $dateRecords) {
						if (count($dateRecords) != 0) {
							$r++; $k = $keyRecord;
						} 
					}
					if ($r != 0) {
						$colCount = (int) count($value1["Data"][$k][1]); // Store number of columns
						foreach ($value1["Data"][$k][1] as $key2 => $value2) {
							$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$i] . '1')->getFont()->setBold(true);
							$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$i] . "1", $key2);
							$i++;
						}
					}
					//: End

					//: Add data from $excelData array
					print(date('H:i:s') . " Add data from " . self::REPORT_NAME ." report" . PHP_EOL);
					$rowCount = (int)2;

					foreach ($value1["Data"] as $key2 => $value2) {
						if (count($value2) != 0) {
							foreach ($value2 as $dataRecords) {
								$i = 0;
								foreach ($dataRecords as $key => $value) {
									if ($key == "Income") {
										$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$i] . strval($rowCount))->getNumberFormat()->setFormatCode('[$R-1C09] 0.00;[RED][$R-1C09]-0.00');
										$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit(str_replace(",", "", $value), PHPExcel_Cell_DataType::TYPE_NUMERIC);
									} else {
										$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
									}
									$objPHPExcel->getActiveSheet()->getCell($alphaVar[$i] . strval($rowCount))->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
									$i++;
								}
								$rowCount++;
							}
						}
					}
					//: End
					
					//: Draw a total summary table to the right of the sheet data
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "1")->setValueExplicit("Number of Trips", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "2")->setValueExplicit("Missing Trip Numbers", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "3")->setValueExplicit("Missing Drivers", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "4")->setValueExplicit("Missing Truck", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "5")->setValueExplicit("Missing Loading Time", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "6")->setValueExplicit("Missing Offloading Time", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "7")->setValueExplicit("Missing Start KMS", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "8")->setValueExplicit("Missing End KMS", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "9")->setValueExplicit("Total Missing Information", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "10")->setValueExplicit("Defects Per Opportunity", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount + 1] . "11")->setValueExplicit("Complete %", PHPExcel_Cell_DataType::TYPE_STRING);
					
					$styleArray = array('borders' => array('allborders' => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN,
											'color' => array('argb' => '000000'),
									),
							),
					);
					$objPHPExcel->getActiveSheet()->getStyle($alphaVar[$colCount + 1] . '1' . ':' . $alphaVar[$colCount + 2] . '11')->applyFromArray($styleArray);
					//: End
					
					//: Insert formulaes into the cells
					/*$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '1','=COUNTA(P1:P1048576)-1'); // Total Number of Trips
				  	$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '2','=' . $alphaVar[$colCount + 2] . '1-((COUNTA(A1:A1048576))-1)');  // Missing Trip Number
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '3','=' . $alphaVar[$colCount + 2] . '1-((COUNTA(F1:F1048576))-1)'); // Missing Driver Detail
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '4','=' . $alphaVar[$colCount + 2] . '1-((COUNTA(I1:I1048576))-1)'); // Missing Loading Truck
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '5','=COUNTIF(Q1:Q1048576, "(none)")'); // Missing Loading Arrival Time
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '6','=COUNTIF(W1:W1048576, "(none)")'); // Missing Offloading Finished Time
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '7','=' . $alphaVar[$colCount + 2] . '1-((COUNTA(AB1:AB1048576))-1)'); // Missing KMS Begin
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '8','=' . $alphaVar[$colCount + 2] . '1-((COUNTA(AC1:AC1048576))-1)'); // Missing KMS End
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '9','=SUM(' . $alphaVar[$colCount + 2] . '1:' . $alphaVar[$colCount + 2] . '8)'); // Total Missing Information
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '10','=' . $alphaVar[$colCount + 2] . '9/(' . $alphaVar[$colCount + 2] . '1*7)'); // Defects Per Opportunity
					$objPHPExcel->getActiveSheet()->setCellValue($alphaVar[$colCount + 2] . '11','=1-' . $alphaVar[$colCount + 2] . '10'); // Complete*/
					//: End
					
					//: Setup Column Widths
					$r = 0; $k = '';
					foreach($value1["Data"] as $keyRecord => $dateRecords) {
						if (count($dateRecords) != 0) {
							$r++; $k = $keyRecord;
						}
					}
					if ($r != 0) {
						for ($i = 0; $i <= count($value1["Data"][$k][1]); $i++) {
							$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$i])->setAutoSize(true);
						}
						$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$colCount + 1])->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$colCount + 2])->setAutoSize(true);
					}
					//: End
				}
				//: End
				// At end set first sheet to active sheet
				$objPHPExcel->setActiveSheetIndex(0);
				
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
	/** ChrisWeeklyTriplistByFleetReport::__construct()
	* Class constructor
	*/
	public function __construct() {
		try {
			$this->_options = getopt("s:e:");
			$startDate = $this->_options["s"];
			$endDate = $this->_options["e"];
			// Construct an array with the predefined fleets and their relevant IDs
			$fleets = (array) array(87 => "Ecosse", 93 => "Anglo Cons", 91 => "Anglo Dedicated", 92 => "Anglo Subbies", 98 => "RE", 97 => "Illovo Cons", 99 => "Illovo Avoca", 96 => "Illovo Harrismith", 95 => "Illovo Germiston", 94 => "Illovo CCC", 100 => "Corobrick Cons", 104 => "EHL Cons", 102 => "EHL Gauteng", 103 => "EHL PE", 106 => "Toyota Cons", 105 => "Toyota XD", 101 => "Toyota Tsusho", 124 => "NCP Consolidated", 121 => "NCP Chlorine", 128 => "NCP Lavendon", 126 => "NCP Other", 127 => "NCP RBay", 130 => "NCP Tanker Services", 129 => "United Bulk", 125 => "NCP Zukratrix", 112 => "MF PE", 113 => "MF PMB", 118 => "MF Delmas", 119 => "MeF Randfontein", 108 => "MF Paarl", 117 => "PPC Dwaalboom", 110 => "PPC George", 116 => "PPC Slurry", 114 => "PPC Hercules", 115 => "PPC Heriotdale", 109 => "PPC Kraaifontein", 111 => "PPC PE", 158 => "RE KZN", 159 => "RE DBN", 160 => "RE Cape", 151 => "Premier Durban", 152 => "Premier EL", 153 => "Premier Empangeni", 154 => "Premier Kokstad", 155 => "Premier Newcastle", 156 => "Premier PMB", 157 => "Premier Spare", 150 => "Mega", 165 => "Anglo - Mokopane", 166 => "Anglo - Rustenburg", 171 => "NCP Infinite", 172 => "NCP U-Wing", 167 => "NCP Unitrans", 211 => "MMega 14 Tons", 210 => "MMega 40 Tons", 209 => "MMega 45 Tons", 207 => "MMega 55 Tons", 206 => "MMega 75 Tons", 208 => "MMega 80-90-100 Tons", 212 => "MMega Standards");
			//: Type cast our primary multidimensional array
			$constructedDates = (array) array();
			// Build an array of the desired date range
			$reportDates = (array) $this->createDateRangeArray(date($startDate), date($endDate));
			// Construct the multidimensional array which we will use to save the report data into
			foreach ($fleets as $keys => $values) {
				$constructedDates[$values]["Data"] = array();
				$constructedDates[$values]["ID"] = $keys;
				foreach ($reportDates as $value) {
					$constructedDates[$values]["Data"][$value] = array();
				}
			}
			unset($fleets);
			// Set variables
			$this->setFileName(self::DOC_FILE_NAME);
			$this->setExcelFile("/export/" . $this->getFileName());
			foreach ($constructedDates as $key1 => $value1) {
				foreach($value1 as $key2 => $value2) {
					if ($key2 != 'ID') {
						foreach($value2 as $key3 => $value3) {
							$aDate = strtotime("+1 day", strtotime($key3));
							$reportData = array(date($key3), (date("Y-m-d", $aDate)), $value1["ID"]);
							$reportUrl = $this->getApiUrl()."report=" . strval(self::REPORT_NUMBER) . "&responseFormat=csv&Start_Date=" . $reportData[0] . "&Stop_Date=" . $reportData[1] . "&Fleet=" . $reportData[2];
							// Clean out whitespace characters and replace with %20
							$reportUrl = str_replace(" ", "%20", $reportUrl);
							$fileParser = new FileParser($reportUrl);
							$fileParser->setCurlFile($this->getFileName() . $key1 . $key3 .  ".csv");
							$data = $fileParser->parseFile();
							$constructedDates[$key1]["Data"][$key3] = $data;
							print("."); // Print a . to the screen after each report is run to indicate progress
						}
					}
				}
			}
			$this->WriteIniFile($constructedDates, dirname(__FILE__) . DIRECTORY_SEPARATOR . "results.txt");
			print(PHP_EOL);
			$this->writeExcelFile(dirname(__FILE__) . $this->getExcelFile() . date("Y-m-d") . ".xlsx", $constructedDates, self::REPORT_NAME . date(" Y-m-d"));
		}
		catch (Exception $e) {
			echo "Caught exception: ",  $e->getMessage(), "\n";
			exit;
		}
	}

	/** ChrisWeeklyTriplistByFleetReport::__destruct()
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

	/** ChrisWeeklyTriplistByFleetReport::WriteIniFile($array, $file)
	 *  Save data file from supplied formatted array
	 *  NOTE: Code amended to suite specific array need for this script
	 */
	private function WriteIniFile($array, $file)
	{
		$fp = fopen($file, 'w');
		foreach ($array as $key1=>$value1) {
			fwrite($fp, $key1 . PHP_EOL);
			foreach ($value1 as $key2=>$value2) {
				if ($key2 != 'ID') {
					foreach ($value2 as $key3=>$value3) {
						fwrite($fp, $key3 . PHP_EOL);
						if (count($value3) != 0) {
							foreach ($value3 as $key4 => $value4) {
								fwrite($fp, $key4 . PHP_EOL);
								foreach ($value4 as $key5 => $value5) {
									fwrite($fp, $key5 . '="' . $value5 . '"' . PHP_EOL);
								}
							}
						}
					}
				}
			}
		}
		fclose($fp);
	}
	//: End
}

new ChrisWeeklyTriplistByFleetReport();
