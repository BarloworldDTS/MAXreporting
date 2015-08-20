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
    CONST SQL_QUERY = "select r.id from udo_refuelordernumber as ron
left join udo_refuel as r on (r.refuelOrderNumber_id=ron.id)
where ron.orderNumber = '%s';";
    	//: Variables
    	private static $_usage = array(
        	"get_refuel_id - Get MAX refuel ID for a refuel using the refuel order number",
	        "",
	        "Usage: get_refuel_id.php -r refuelordernumber",
	        "",
	        "Arguments:",
	        "",
	        "Required options:",
	        "-r: refuel order number of refuel",
	        "",
        	"Example:",
	        "",
	        "get_refuel_id.php -r 440212",
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
		$options = getopt("r:");
        $sqlfile = $options["r"];
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
                $_data = $sqlData->getDataFromQuery($_query);
        		if ($_data) {
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
