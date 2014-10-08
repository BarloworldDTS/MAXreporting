<?php
// Error reporting
error_reporting ( E_ALL );

// : Includes

include_once 'PHPUnit/Extensions/PHPExcel/Classes/PHPExcel.php';
/**
 * PHPExcel_Writer_Excel2007
 */
include 'PHPUnit/Extensions/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
/**
 * MySQL query pull and return data class
 */
include dirname ( __FILE__ ) . '/PullDataFromMySQLQuery.php';
// : End

/**
 * Object::PullFandVContractData
 *
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class PullFandVContractData {
	// : Constants
	const DS = DIRECTORY_SEPARATOR;
	
	// : Variables
	protected $_mode;
	protected $_sqlfile = "fandvcontracts%Llinks.sql";
	
	// : Public functions
	// : Accessors
	/**
	 * PullFandVContractData::writeExcelFile($excelFile, $excelData)
	 * Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
	 *
	 * @param $excelFile, $excelData        	
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
		$a = 1; $numCol = count($excelData);
		foreach($excelData as $value1) {
			$aCell = $alphaVar[$a] . "1";
			$objPHPExcel->getActiveSheet()->setCellValue($aCell, $value1["tradingName"]);
			$objPHPExcel->getActiveSheet()->getStyle($aCell)->getFont()->setBold(true);
			$a++;			
		}
		
		//: Set Row Headers
		print(date('H:i:s') . " Setup row headers" . PHP_EOL);
		$rowHeaders = ( array ) array ("Contract", "Customer", "Contrib", "Cost", "Days", "Rate", "Business Unit", "Start Date", "End Date", "Truck Type", "Trucks Linked", "Routes Linked", "RateType", "DaysPerMonth", "DaysPerTrip", "FuelConsumption", "FleetValues", "ExpectedEmptyKms", "ExpectedDistance");
		$a = 1;
		foreach($rowHeaders as $value) {
			$objPHPExcel->getActiveSheet()->getStyle("A" . strval($a))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue("A" . strval($a), $value);
			$a++;
		}

		//Add more column header value assignments here
		//: End

		//: Add data from $excelData array
		print(date('H:i:s') . " Add data from [reportName] report" . PHP_EOL);
		$colCount = (int) 1;
		$objPHPExcel->setActiveSheetIndex(0);
		foreach($excelData as $value1) {
			foreach($value1 as $key2 => $value2) {
				if ($value2 != NULL) {
					$fornum = number_format((intval($value2) / 100), 2, ".", "");
				} else {
					$fornum = NULL;
				}
				switch ($key2) {
					case "tradingName": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "2")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "fixedContribution": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "3")->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "fixedCost": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "4")->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "numberOfDays": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "5")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "rate": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "6")->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "buname": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "7")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "startDate":
						if ($this->_mode != "create") {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "8")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
						} else {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "8")->setValueExplicit(date("Y-m-01 00:00:00", strtotime("+1 month")), PHPExcel_Cell_DataType::TYPE_STRING);
						}
						break;
					case "endDate":
						if ($this->_mode != "create") {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "9")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
						} else {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "9")->setValueExplicit(date("Y-m-t 23:59:59", strtotime("+1 month")), PHPExcel_Cell_DataType::TYPE_STRING);
						}
						break;
					case "description": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "10")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "trucks": 
						if (count($value2) != 0) {
							foreach ($value2 as $value3) {
								$objPHPExcel->getActiveSheet()->getComment($alphaVar[$colCount] . '11')->getText()->createTextRun($value3);
								$objPHPExcel->getActiveSheet()->getComment($alphaVar[$colCount] . '11')->getText()->createTextRun("\r\n");
							}
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "11")->setValueExplicit("1", PHPExcel_Cell_DataType::TYPE_STRING);
						} else {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "11")->setValueExplicit("0", PHPExcel_Cell_DataType::TYPE_STRING);
						}
						break;
					case "routes":
						if (count($value2) != 0) {
							foreach ($value2 as $value3) {
								$objPHPExcel->getActiveSheet()->getComment($alphaVar[$colCount] . '12')->getText()->createTextRun($value3);
								$objPHPExcel->getActiveSheet()->getComment($alphaVar[$colCount] . '12')->getText()->createTextRun("\r\n");
							}
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "12")->setValueExplicit("1", PHPExcel_Cell_DataType::TYPE_STRING);
						} else {
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "12")->setValueExplicit("0", PHPExcel_Cell_DataType::TYPE_STRING);
						}
						break;
					case "rat": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "13")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "dpm":
						$_cellvalue = strval(number_format((floatval($fornum) * 100), 0, "", ""));
						$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "14")->setValueExplicit($_cellvalue, PHPExcel_Cell_DataType::TYPE_STRING);
						break;
					case "dpt":
						$_cellvalue = strval(number_format((floatval($fornum) * 100), 0, "", ""));
						$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "15")->setValueExplicit($_cellvalue, PHPExcel_Cell_DataType::TYPE_STRING);
						break;
					case "fc": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "16")->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "fval": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "17")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "eek": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "18")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "ed": $objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "19")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING); break;
					case "id":
						if (strtolower($this->_mode) != "create") {		
							$objPHPExcel->getActiveSheet()->getCell($alphaVar[$colCount] . "20")->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
							break;
						}
				}
			}
			$colCount++;
		}
		//: End

		//: Setup Column Widths

		for($a = 0; $a >= $numCol; $a++) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($alphaVar[$a])->setAutoSize(true);
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
	
	// : Magic
	/**
	 * VarianceReport::__construct()
	 * Class constructor
	 */
	public function __construct() {
		try {
		$_options = getopt("m:");
		if(!array_key_exists("m",$_options)) {
			throw new Exception ("Please provide the option using switch -m to specify the mode which the script must run." . PHP_EOL . "Modes: create / update" . PHP_EOL);
		} 
		$this->_mode = $_options["m"];
		$_excelFileName = (string) date("Y-m-d") . "FandVContracts";
		$sqlData = new PullDataFromMySQLQuery("max2");
		$files = ( array ) array (
				"truck",
				"route",
				"contract" 
		);
		$consolidated = ( array ) array ();
		$contracts = ( array ) array ();
		$trucklinks = ( array ) array ();
		$routelinks = ( array ) array ();
		
		foreach ( $files as $_filename ) {
			if ($_filename != "contract") {
				$_file = preg_replace ( "/%L/", $_filename, $this->_sqlfile );
			} else {
				$_file = "fandvcontracts.sql";
			}
			$_data = $sqlData->getDataFromSQLFile($_file, "", "", FALSE);
			switch ($_filename) {
				case "truck" : $trucklinks = $_data;
				case "route" : $routelinks = $_data;
				case "contract" : $contracts = $_data;	
			}
		}

		if ((count ( $trucklinks ) != 0) && (count ( $routelinks ) != 0) && (count ( $contracts ) != 0)) {
			foreach($contracts as $key => $contract) {
				$consolidated[] = $contract;
				foreach($trucklinks as $truck) {
					if ($truck["id"] == $contract["id"]) {
						$consolidated[$key]["trucks"][$truck["tid"]] = $truck["fleetnum"]; 					}
				}
				foreach($routelinks as $route) {
					if ($route["id"] == $contract["id"]) {
						if ($route["rid"] != NULL) {
							$consolidated[$key]["routes"][$route["rid"]] = $route["lfn"] . " TO " . $route["ltn"];
							// Check for leadKms and add to the end of the route value
							if ($route["leadKms"] != NULL) {
								$consolidated[$key]["routes"][$route["rid"]] .= "[" . $route["leadKms"] . "]";
							} else {
								$consolidated[$key]["routes"][$route["rid"]] .= "[0]";
							}
						}
					}
				}
			}
		}

		//: Take data and write into an excel spreadsheet
		$this->writeExcelFile(dirname(__FILE__) . self::DS . $_excelFileName . ".xlsx", $consolidated);
		}
		catch (Exception $e) {
			print($e->getMessage());
		}
	}
	
	/**
	 * VarianceReport::__destruct()
	 * Class destructor
	 * Allow for garbage collection
	 */
	public function __destruct() {
		unset ( $this );
	}
	// : End
	
	// : Private Functions
	
	// : End
}

new PullFandVContractData ();