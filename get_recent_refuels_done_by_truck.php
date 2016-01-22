<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
/** MySQL query pull and return data class */
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

/** Object::get_recent_refuels_done_by_truck
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class get_recent_refuels_done_by_truck {
    CONST TENANT_DB = "max2";
    CONST HOST_DB = "192.168.1.19";
    CONST DEFAULT_LIMIT = 5;
    CONST SQL_QUERY = "SELECT rf.id as id,
rfo.orderNumber,
tr.fleetnum,
rf.odo,
rf.litres,
rf.fillDateTime,
CONCAT(pc.first_name, \" \", pc.last_name) as createdBy,
rf.time_created,
CONCAT(plm.first_name, \" \", plm.last_name) as lastModifiedBy,
rf.time_last_modified
FROM udo_refuel AS rf
LEFT JOIN udo_refuelordernumber AS rfo ON (rfo.id=rf.refuelOrderNumber_id)
LEFT JOIN udo_truck AS tr ON (tr.id=rf.truck_id)
LEFT JOIN permissionuser AS puc ON (puc.id=rf.created_by)
LEFT JOIN person AS pc ON (pc.id=puc.person_id)
LEFT JOIN permissionuser AS pulm ON (pulm.id=rf.last_modified_by)
LEFT JOIN person AS plm ON (plm.id=pulm.person_id)
WHERE tr.fleetnum = \"%s\"
ORDER BY rf.fillDateTime DESC LIMIT %d;";
    	//: Variables
    	private static $_usage = array(
        	"get_recent_refuels_done_by_truck - Get list of refuels last captured for the specified truck from MAX",
	        "",
	        "Usage: get_recent_refuels_done_by_truck.php -t fleetnum",
	        "",
	        "Arguments:",
	        "",
	        "Required options:",
	        "-r: Truck Fleetnum",
	        "",
	        "Optional options:",
	        "",
	        "-l: integer",
	        "",
        	"Example:",
	        "",
	        "Get last 5 refuels captured for truck 444010:",
	        "",
	        "get_recent_refuels_done_by_truck.php -t 444010",
	        "",
	        "Get last 10 refuels captured for truck 444010:",
	        "",
	        "get_recent_refuels_done_by_truck.php -t 444010 -l 10",
			""
    	);

	//: Public functions
	//: Accessors

	//: Magic
	/** runsqlfile::__construct()
	* Class constructor
	*/
	public function __construct() {
	// Construct an array with predefined date(s) which we will use to run a report
		$options = getopt("t:l:");
		
		$_result_limit = intval($options["l"]) ? intval($options["l"]) : SELF::DEFAULT_LIMIT;
		
        $sqlfile = $options["t"];
        if ($sqlfile) {
            $_ids = explode(",", $sqlfile);
        } else {
		$this->printUsage();
        }
        
        

        
        $sqlData = new PullDataFromMySQLQuery(self::TENANT_DB, self::HOST_DB);
        // Run query and return result
        if (is_array($_ids)) {
            $_x = 1;
            foreach($_ids as $_id) {
                
                $_query = preg_replace("/%s/", $_id, self::SQL_QUERY);
                $_query = preg_replace("/%d/", $_result_limit, $_query);
                
                $_data = $sqlData->getDataFromQuery($_query);

        		if ($_data) {
					
					// Clear the screen
					system('clear');
					system('clear');
					
                    foreach($_data as $_key => $_value) {
                        echo $_x . PHP_EOL;
                        if (is_array($_value)) {
                            foreach($_value as $_key2 => $_value2) {
                                echo "$_key2: $_value2" . PHP_EOL;
                            }
                        }
                        $_x++;
                    }
                } else {
                    echo "NO RESULT" . PHP_EOL;
                }
            }
        }
	}

	/** runsqlfile::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
    // : Private Functions
    /**
     * get_refuel_id::printUsage($_msg = null)
     * Prints the usage static property belonging to the class to output the usage of the script from the command line
     */
    private function printUsage($_msg = null)
    {
        // Clear the screen
        system('clear');
        system('clear');

        // : Print a message before printing the usage is supplied
        if ($_msg && is_string($_msg)) {

            // If string print on its own line
            print($_msg . PHP_EOL);

        } else if ($_msg && is_array($_msg)) {

            // If array loop each item and print each item on its own line
            foreach($_msg as $_msg_lineitem) {

                print($_msg_lineitem . PHP_EOL);

            }
        }
        // : End

        // Print a blank line and then beginning printing message
        print(PHP_EOL);

        // : Print usage string line by line
        foreach (self::$_usage as $_lineitem) {
            print($_lineitem . PHP_EOL);
        }
        // To keep things clean print a blank line at the end
        print(PHP_EOL);
        // : End

        // Terminate
        exit();
    }

} new get_recent_refuels_done_by_truck();
