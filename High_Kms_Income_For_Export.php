<?php
// Error reporting
error_reporting ( E_ALL );

// : Includes
require_once dirname ( __FILE__ ) . '/FileParser.php';
/**
 * PHP Excel class library
 */
require_once dirname ( __FILE__ ) . '/Classes/PHPExcel.php';
/**
 * MySQL query pull and return data class
 */
include dirname ( __FILE__ ) . '/PullDataFromMySQLQuery.php';
/**
 * PHPExcel_Writer_Excel2007
 */
include dirname ( __FILE__ ) . '/Classes/PHPExcel/Writer/Excel2007.php';
// : End

/**
 * Object::High_Kms_Income_For_Export
 *
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class High_Kms_Income_For_Export {
	// : Constants
	const REPORT_NAME = "High Kms Report";
	const DOC_OWNER = "Clinton Wright";
	const DOC_TITLE = "High Kms Report";
	const DOC_FILE_NAME = "High_Kms_Report";
	const DOC_SUBJECT = "Custom MAX report: High Kms Report";
	const MAXDB = "max2";
	const T24DB = "application_3";
	const KMS_HIGH = 3000;
	
	// : Variables
	protected $_tenant = array (
			"MAX" => "https://login.max.bwtsgroup.com",
			"T24" => "https://t24.max.bwtsgroup.com" 
	);
	protected $_apiurl = "/api_request/Report/export?";
	protected $_reportNumber = array (
			"MAX" => "84",
			"T24" => "79" 
	);
	protected $_excelFileName;
	protected $_fileName;
	protected $_startDate;
	protected $_endDate;
	protected $_fleets = array (
			"MAX" => array (
					118,
					108,
					112,
					113,
					119,
					169,
					82,
					282,
					100,
					170,
					117,
					110,
					114,
					115,
					109,
					111,
					116,
					222,
					276,
					104,
					95,
					94,
					99,
					151,
					155,
					153,
					152,
					105,
					101,
					168,
					275,
					93,
					283,
					150 
			),
			"T24" => array (
					27,
					13,
					20,
					29,
					24,
					25,
					26 
			) 
	);
	protected $_fleetnames = array ();
	
	// : Public functions
	// : Accessors
	
	/**
	 * High_Kms_Income_For_Export::getApiUrl($_tenant)
	 * base url to call
	 *
	 * @param string: $_tenant
	 *        	=== "MAX" || "T24"
	 * @return string
	 */
	protected function getApiUrl($_tenant) {
		$_urlReport = $this->_tenant [$_tenant] . $this->_apiurl . "report=" . $this->_reportNumber [$_tenant];
		return $_urlReport;
	}
	
	/**
	 * High_Kms_Income_For_Export::getExcelFile()
	 *
	 * @return string: $this->_excelFileName
	 */
	public function getExcelFile() {
		return $this->_excelFileName;
	}
	
	/**
	 * High_Kms_Income_For_Export::getFileName()
	 *
	 * @return string: $this->_excelFileName
	 */
	public function getFileName() {
		return $this->_fileName;
	}
	
	// : End
	// : Setters
	
	/**
	 * High_Kms_Income_For_Export::setExcelFile($_setFile)
	 *
	 * @param string: $_setFile        	
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}
	
	/**
	 * High_Kms_Income_For_Export::setFileName($_setFile)
	 *
	 * @param string: $_setFile        	
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}
	
	// : End
	
	/**
	 * High_Kms_Income_For_Export::writeExcelFile($excelFile, $excelData)
	 * Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
	 *
	 * @param $excelFile, $excelData        	
	 */
	public function writeExcelFile($excelFile, $excelData, $reportName, $sheetName) {
		try {
			// Check data validility
			if (count ( $excelData ) != 0) {
				
				// : Setup array for selecting columns in sheet
				$alphaA = range ( 'A', 'Z' );
				$alphaVar = range ( 'A', 'Z' );
				foreach ( $alphaA as $valueA ) {
					foreach ( $alphaA as $valueB ) {
						$alphaVar [] = $valueA . $valueB;
					}
				}
				unset ( $alphaA );
				echo "Print out alphavar" . PHP_EOL;
				print_r($alphaVar);
				// : End
				
				// : Create new PHPExcel object
				print ("<pre>") ;
				print (date ( 'H:i:s' ) . " Create new PHPExcel object" . PHP_EOL) ;
				$objPHPExcel = new PHPExcel ();
				// : End
				
				// : Set properties
				print (date ( 'H:i:s' ) . " Set properties" . PHP_EOL) ;
				$objPHPExcel->getProperties ()->setCreator ( self::DOC_OWNER );
				$objPHPExcel->getProperties ()->setLastModifiedBy ( self::DOC_OWNER );
				$objPHPExcel->getProperties ()->setTitle ( self::DOC_TITLE );
				$objPHPExcel->getProperties ()->setSubject ( self::DOC_SUBJECT );
				// : End
				
				// : Setup Workbook Preferences
				print (date ( 'H:i:s' ) . " Setup workbook preferences" . PHP_EOL) ;
				$objPHPExcel->getDefaultStyle ()->getFont ()->setName ( 'Arial' );
				$objPHPExcel->getDefaultStyle ()->getFont ()->setSize ( 8 );
				$objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
				$objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
				$objPHPExcel->getActiveSheet ()->getPageSetup ()->setFitToWidth ( 1 );
				$objPHPExcel->getActiveSheet ()->getPageSetup ()->setFitToHeight ( 0 );
				// : End
				
				// : Create sheets
				$_count = ( int ) 1;
				echo "Generate worksheets" . PHP_EOL;
				foreach ( $excelData as $mainKey => $mainValue ) {
					$wsName = $mainKey;
					// Check fleet name character length and shorten if greater than 25 characters
					if (strlen ( $wsName ) > 25) {
						$wsName = substr ( $wsName, 0, 25 );
					}
					// : Add new worksheet and set the title of the sheet name
					$myWorkSheet = new PHPExcel_Worksheet ( $objPHPExcel, $wsName );
					$objPHPExcel->addSheet ( $myWorkSheet, $_count );
					// : End
					echo $wsName . PHP_EOL;
					$_count ++;
				}
				// : End
				
				foreach ( $excelData as $mainKey => $mainValue ) {
					// : Set Correct Worksheet Active
					$wsName = $mainKey;
					if (strlen ( $wsName ) >= 25) {
						$wsName = substr ( $wsName, 0, 25 );
					}
					$objPHPExcel->setActiveSheetIndexByName ( $wsName );
					// : End
					
					// : Set Column Headers
					print (date ( 'H:i:s' ) . " Setup column headers" . PHP_EOL) ;
					$i = ( int ) 0;
					
					foreach ( $mainValue [$this->_startDate] [1] as $key => $value ) {
						print_r($key);
						$objPHPExcel->getActiveSheet ()->getStyle ( $alphaVar [$i] . '1' )->getFont ()->setBold ( true );
						$objPHPExcel->getActiveSheet ()->setCellValue ( $alphaVar [$i] . "1", $key );
						$i ++;
					}
					// : End
					
					// : Add data from $excelData array
					print (date ( 'H:i:s' ) . " Add data from " . $wsName . " report" . PHP_EOL) ;
					$rowCount = ( int ) 2;
					foreach ( $mainValue as $_dateKey => $_dayValues ) {
						if (count ( $_dayValues ) != 0) {
							foreach ( $_dayValues as $dataRecords ) {
								if (count ( $dataRecords ) != 0) {
									$i = 0;
									foreach ( $dataRecords as $key => $value ) {
										$objPHPExcel->getActiveSheet ()->getCell ( $alphaVar [$i] . strval ( $rowCount ) )->setValueExplicit ( $value, PHPExcel_Cell_DataType::TYPE_STRING );
										$i ++;
									}
									// Goto next row
									$rowCount ++;
								}
							}
						}
					}
					// : End
					
					// : Auto Set Column Widths
					echo "Auto set column widths";
					print_r($mainValue);
					for($i = 0; $i <= count ( $mainValue [$this->_startDate] [1] ); $i ++) {
						$objPHPExcel->getActiveSheet ()->getColumnDimension ( $alphaVar [$i] )->setAutoSize ( true );
					}
					// : End
				}
				
				// : Save spreadsheet to Excel 2007 file format
				print (date ( 'H:i:s' ) . " Write to Excel2007 format" . PHP_EOL) ;
				print ("</pre>" . PHP_EOL) ;
				$objWriter = new PHPExcel_Writer_Excel2007 ( $objPHPExcel );
				$objWriter->save ( $excelFile );
				$objPHPExcel->disconnectWorksheets ();
				unset ( $objPHPExcel );
				unset ( $objWriter );
				// : End
			} else {
				print ("<pre>") ;
				print_r ( "ERROR: The function was passed an empty array" );
				print ("</pre>") ;
				exit ();
			}
		} catch ( Exception $e ) {
			echo "Caught exception: ", $wsName, "\n", $e->getMessage (), "\n";
			exit ();
		}
	}
	
	// : Magic
	/**
	 * High_Kms_Income_For_Export::__construct()
	 * Class constructor
	 */
	public function __construct() {
		try {
			// Store sql queries
			$_queries = "select name from udo_fleet where id=%s;";
			
			// Create new SQL Query class object
			$_mysqlQueryMAX = new PullDataFromMySQLQuery ( self::MAXDB );
			$_mysqlQueryT24 = new PullDataFromMySQLQuery ( self::T24DB );
			
			// : Loop through each fleet ID stored in the fleet IDs array and return the name for each and add it to the array variable
			foreach ( $this->_fleets as $fleetKey => $fleetValue ) {
				foreach ( $fleetValue as $_fleet ) {
					
					$_aQuery = preg_replace ( "/%s/", $_fleet, $_queries );
					switch ($fleetKey) {
						case "MAX" :
							$_result = $_mysqlQueryMAX->getDataFromQuery ( $_aQuery );
							break;
						case "T24" :
							$_result = $_mysqlQueryT24->getDataFromQuery ( $_aQuery );
							break;
					}
					if (count ( $_result ) != 0) {
						if (($_result [0] ["name"]) && (array_key_exists ( "0", $_result ) != FALSE) && (array_key_exists ( "name", $_result [0] ) != FALSE)) {
							$this->_fleetnames [$fleetKey] [$_fleet] = $_result [0] ["name"];
						}
					}
				}
			}
			// : End
			// : Fetch and store the parameters required from user
			$options = getopt ( "s:e:" );
			// Start Date
			$this->_startDate = $options ["s"];
			// End Date
			$this->_endDate = $options ["e"];
			// : End
			
			// : Construct a multidimensional array containing the year => month => days
			$constructedDates = ( array ) array ();
			// Build an array of the desired date range
			$reportDates = ( array ) $this->createDateRangeArray ( $this->_startDate, $this->_endDate );
			
			// Get the the year within the dates
			$firstYear = intval ( substr ( $reportDates [0], 0, 4 ) );
			$lastYear = intval ( substr ( $reportDates [(count ( $reportDates ) - 1)], 0, 4 ) );
			
			// Determine the first and last month and the total number of months
			$firstMonth = ( string ) substr ( $reportDates [0], - 5, 2 );
			$lastMonth = ( string ) substr ( $reportDates [(count ( $reportDates ) - 1)], - 5, 2 );
			
			// Construct initial parts of our mulitdimensional array
			$aYear = $firstYear;
			$constructedDates [$firstYear] = array ();
			$aMonth = $firstMonth;
			$constructedDates [$firstYear] [$firstMonth] = array ();
			
			// Construct the multidimensional array with the dates
			foreach ( $reportDates as $value ) {
				if ($aYear != substr ( $value, 0, 4 )) {
					$constructedDates [substr ( $value, 0, 4 )] = array ();
					$aYear = substr ( $value, 0, 4 );
				}
				if ($aMonth != substr ( $value, - 5, 2 )) {
					$constructedDates [substr ( $value, 0, 4 )] [substr ( $value, - 5, 2 )] = array ();
					$aMonth = substr ( $value, - 5, 2 );
				}
				$constructedDates [$aYear] [$aMonth] [] = $value;
			}
			// : End
			
			// Set variables
			$all = ( array ) array ();
			$dataCount = ( int ) 0;
			foreach ( $constructedDates as $key1 => $value1 ) {
				foreach ( $value1 as $key2 => $value2 ) {
					$all = array ();
					// Set the filename for the csv file
					$this->setFileName ( self::DOC_FILE_NAME . date ( "-" . $key1 . "-" . $key2 ) );
					// Set the filename for the xls file
					$this->setExcelFile ( "/export/" . $this->getFileName () );
					// Print the year and month
					echo $key1 . "-" . $key2 . PHP_EOL;
					
					foreach ( $value2 as $key3 => $value3 ) {
						foreach ( $this->_fleets as $fleetKey => $fleetValue ) {
							foreach ( $fleetValue as $fleet_id ) {
								$aDate = strtotime ( "+1 day", strtotime ( $value3 ) );
								$reportData = array (
										($value3 . " 00:00:00"),
										(date ( "Y-m-d", $aDate )) . " 00:00:00" 
								);
								$reportUrl = $this->getApiUrl ( $fleetKey ) . "&responseFormat=csv&Start_Date=" . $reportData [0] . "&Stop_Date=" . $reportData [1] . "&Fleet=" . $fleet_id;
								// Comply with HTML string standard for whitespace characters and replace with %20
								$reportUrl = str_replace ( " ", "%20", $reportUrl );
								$fileParser = new FileParser ( $reportUrl );
								$fileParser->setCurlFile ( $this->getFileName () . "-" . $value3 . ".csv" );
								$data = $fileParser->parseFile ();
								$dataCount = count ( $data );
								
								$filteredData = array ();
								
								// If report run is empty skip adding to the array
								if ($dataCount != 0) {
									foreach ( $data as $dataRecord ) {
										
										// : Run through each line item imported and if kms === high then add to filteredData array
										$_tenant = "";
										$kms = 0;
										$emptykms = 0;
										$highKmsTrue = FALSE;
										if ((array_key_exists ( "Kms in Trip leg", $dataRecord )) && (array_key_exists ( "Empty Kms", $dataRecord ))) {
											$kms = intval ( $dataRecord ["Kms in Trip leg"] );
											$emptykms = intval ( $dataRecord ["Empty Kms"] );
											if ((($kms != NULL) && ($kms > 0) && ($kms >= self::KMS_HIGH)) && (($emptykms != NULL) && ($emptykms > 0) && ($emptykms >= self::KMS_HIGH))) {
												$highKmsTrue = TRUE;
											} else {
												$highKmsTrue = FALSE;
											}
										} else if (array_key_exists ( "Total Kms", $dataRecord )) {
											$kms = intval ( $dataRecord ["Total Kms"] );
											if (($kms != NULL) && ($kms > 0) && ($kms >= self::KMS_HIGH)) {
												$highKmsTrue = TRUE;
											} else {
												$highKmsTrue = FALSE;
											}
										}
										
										switch ($highKmsTrue) {
											case TRUE :
												$filteredData [] = $dataRecord;
												break;
										}
										// : End
										
									}
									
									// If filteredData is empty then dont add to final data array
									if (count ( $filteredData ) != 0) {
										$all [$this->_fleetnames [$fleetKey] [$fleet_id]] [$value3] = $filteredData;
									}
								}
							}
						}
						echo "."; // print and period to indicate a report is successfully completed
					}
					print_r($all);
					echo "\n";
					if (count ( $all ) != 0) {
						$this->writeExcelFile ( dirname ( __FILE__ ) . $this->getExcelFile () . ".xlsx", $all, self::REPORT_NAME . "-" . $key1 . "-" . $key2, $key1 . "-" . $key2 );
					}
				}
			}
		} catch ( Exception $e ) {
			echo "Caught exception: ", $e->getMessage (), "\n";
			echo "F", "\n";
			exit ();
		}
	}
	
	/**
	 * High_Kms_Income_For_Export::__destruct()
	 * Class destructor
	 * Allow for garbage collection
	 */
	public function __destruct() {
		unset ( $this );
	}
	// : End
	
	// : Private Functions
	
	/**
	 * createDateRangeArray($strDateFrom,$strDateTo)
	 *
	 * @see http://stackoverflow.com/questions/4312439/php-return-all-dates-between-two-dates-in-an-array
	 * @param unknown $strDateFrom        	
	 * @param unknown $strDateTo        	
	 * @return multitype:
	 */
	private function createDateRangeArray($strDateFrom, $strDateTo) {
		// takes two dates formatted as YYYY-MM-DD and creates an
		// inclusive array of the dates between the from and to dates.
		
		// could test validity of dates here but I'm already doing
		// that in the main script
		$aryRange = array ();
		
		$iDateFrom = mktime ( 1, 0, 0, substr ( $strDateFrom, 5, 2 ), substr ( $strDateFrom, 8, 2 ), substr ( $strDateFrom, 0, 4 ) );
		$iDateTo = mktime ( 1, 0, 0, substr ( $strDateTo, 5, 2 ), substr ( $strDateTo, 8, 2 ), substr ( $strDateTo, 0, 4 ) );
		
		if ($iDateTo >= $iDateFrom) {
			array_push ( $aryRange, date ( 'Y-m-d', $iDateFrom ) ); // first entry
			while ( $iDateFrom < $iDateTo ) {
				$iDateFrom += 86400; // add 24 hours
				array_push ( $aryRange, date ( 'Y-m-d', $iDateFrom ) );
			}
		}
		return $aryRange;
	}
	// : End
}

new High_Kms_Income_For_Export ();
