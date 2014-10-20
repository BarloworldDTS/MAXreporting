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
	// : End
	
	// : Magic
	/**
	 * VarianceReport::__construct()
	 * Class constructor
	 */
	public function __construct() {
		try {
			
			$_query1 = "select fillDateTime, odo from udo_refuel where truck_id=(select id from udo_truck where fleetnum='%f') ORDER BY ID desc LIMIT 3;";
			$sqlData = new PullDataFromMySQLQuery ( "application_3" );
			$_t24trucks = $sqlData->getDataFromQuery ( "select id, fleetnum from udo_truck where active IS NOT NULL;" );
			$_lastOdo = array ();
			
			if ($_t24trucks) {
				foreach ( $_t24trucks as $key => $value ) {
					$_a = preg_replace ( "/%f/", $value ["fleetnum"], $_query1 );
					$_result = $sqlData->getDataFromQuery ( $_a );
					
					if ($_result) {
						$_odo = "";
						
						if (count ( $_result ) >= 1) {
							foreach ( $_result as $_rkey => $_refuel ) {
								if (! $_odo) {
									if ($_refuel ["odo"]) {
										$_odo = $_refuel ["odo"];
									}
								}
							}
						}
						$_lastOdo[$value["fleetnum"]]["odo"] = $_odo;
					}
				}
			}
			foreach($_lastOdo as $key => $value) {
				echo '"' . $key . '", ' . $value["odo"] . PHP_EOL;
			}
			
		} catch ( Exception $e ) {
			print ($e->getMessage ()) ;
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