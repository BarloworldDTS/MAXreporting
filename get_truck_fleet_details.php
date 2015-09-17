<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
/** MySQL query pull and return data class */
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

/** Object::runsqlfile
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class runsqlfile {
    CONST TENANT_DB = "max2";
    CONST HOST_DB = "192.168.1.19";
	CONST SQL_QUERY_FLEET_INFO = "select * from udo_fleet where name like '%fn';";
	
	CONST SQL_QUERY_FLEET_ALL_LINKED_TRUCKS = "select ftl.truck_id, t.fleetnum, ftl.fleet_id, f.name as fleetname, drv.beginDate, drv.endDate
from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id)
left join udo_fleet as f on (f.id=ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND t.fleetnum like '%f';";

	CONST SQL_QUERY_TRUCK_INFO = "select * from udo_truck where fleetnum like '%fn';";
	
    CONST SQL_QUERY_FLEET_TRUCK_LINKS = "select ftl.truck_id, t.fleetnum, ftl.fleet_id, f.name as fleetname, drv.beginDate, drv.endDate
from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id)
left join udo_fleet as f on (f.id=ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND t.fleetnum like '%f';";

	//: Variables
        private static $_usage = array(
                "get_truck_fleet_details - Get info for a truck and/or fleet on MAX",
                "",
                "Usage: get_truck_fleet_details.php -t fleetnum -f name",
                "",
                "Arguments:",
                "",
                "At least 1 of the below is required:",
                "-t: truck fleetnum",
                "",
				"-f: fleet name",
                "",
                "Example:",
                "",
                "get_trip_info.php -t 343054",
                "",
				"Result:",
				"+----------+----------+----------+------------------------+---------------------+---------+",
				"| truck_id | fleetnum | fleet_id | fleetname              | beginDate           | endDate |",
				"+----------+----------+----------+------------------------+---------------------+---------+",
				"|      830 | 343054   |       94 | Illovo - Germiston CCC | 2014-02-28 22:00:00 | NULL    |",
				"|      830 | 343054   |      275 | FMCG Commercial JHB    | 2014-05-31 22:00:00 | NULL    |",
				"+----------+----------+----------+------------------------+---------------------+---------+",
                "",
                "get_trip_info.php -f Engen",
                "",
				"Result:",
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
		$options = getopt("c:");
        $sqlfile = $options["c"];
        if ($sqlfile) {
            $_ids = explode(",", $sqlfile);
        } else {
		$this->printUsage();
        }
        
	$sqlData = new PullDataFromMySQLQuery(self::TENANT_DB, self::HOST_DB);
        // Run query and return result
        if (is_array($_ids)) {
            
		foreach($_ids as $_id) {
                
                $_query = preg_replace("/%d/", $_id, self::SQL_QUERY);
                $_data = $sqlData->getDataFromQuery($_query);

        	if ($_data) {
                foreach($_data as $_key => $_value) {
                        if (is_array($_value)) {
                            foreach($_value as $_key2 => $_value2) {
                                echo "$_key2: $_value2" . PHP_EOL;
                            }
                        }
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
     * scriptgenmax::importData()
     * Prints the usage static property belonging to the class to output the usage of the script from the command line
     */
    private function printUsage($_msg = null)
    {
        // Clear the screen
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

} new runsqlfile();
