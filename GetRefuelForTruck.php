#!/usr/bin/php
<?php
//: Includes
/** MySQL query pull and return data class */
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

 * GetRefuelForTruck.php
 *
 * @package GetRefuelForTruck
 * @author Clinton Wright <cwright@bwtrans.com>
 * @copyright 2016 onwards Barloworld Transport (Pty) Ltd
 * @license GNU GPL v2.0
 * @link https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 *       This program is free software; you can redistribute it and/or
 *       modify it under the terms of the GNU General Public License
 *       as published by the Free Software Foundation; either version 2
 *       of the License, or (at your option) any later version.
 *      
 *       This program is distributed in the hope that it will be useful,
 *       but WITHOUT ANY WARRANTY; without even the implied warranty of
 *       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *       GNU General Public License for more details.
 */
class GetRefuelForTruck {
    CONST TENANT_DB = "max2";
    CONST HOST_DB = "192.168.1.19";
    CONST SQL_QUERY = "select r.id from udo_refuelordernumber as ron
left join udo_refuel as r on (r.refuelOrderNumber_id=ron.id)
where ron.orderNumber = '%s';";
    	//: Variables
    	private static $_usage = array(
        	"GetRefuelForTruck - Get refuel orders for a month for one or more trucks from MAX",
	        "",
	        "Usage: GetRefuelForTruck.php -t fleetnum1,fleetnum2 -m 2016-05",
	        "",
	        "Arguments:",
	        "",
	        "Required options:",
	        "-t fleetnum1, fleetnum2	(MAX fleet numbers of trucks)",
		    "",
		    "Optional options:",
		    "-m YYYY-MM			        (Month to fetch refuel order, default is current month)",
	        "",
		    "Exclusive optional options:",
            "-a                         (Fetch all refuels done by the truck(s) for the month",
            "                           including incompleted refuels - this is the default)",
            "",
            "-A                         (Fetch all refuels done by the truck(s) for the month",
            "                           only inclusive of completed refuels)",
            "",
            "-l                         (Fetch the last completed refuel for each truck for the month",
		    "",
        	"Example(s):",
	        "",
	        "GetRefuelForTruck.php -t fleetnum1,fleetnum2",
		    "",
		    "This will pull all refuel orders done within the current month for trucks",
            "fleetnum1 and fleetnum2",
		    "",
	        "GetRefuelForTruck.php -t fleetnum1,fleetnum2 -m 2016-04",
		    "",
		    "This will pull all refuel orders done within the month of '2016-04' for trucks",
            "fleetnum1 and fleetnum2",
		    "",
	        "GetRefuelForTruck.php -t fleetnum1,fleetnum2 -m 2016-04 -l",
		    "",
		    "This will fetch the last refuel order done within the month of '2016-04' for trucks",
            "fleetnum1 and fleetnum2",
		    ""
    	);

	//: Public functions
	//: Accessors

	//: Magic
	/** GetRefuelForTruck::__construct()
	* Class constructor
	*/
	public function __construct() {
	// Construct an array with predefined date(s) which we will use to run a report
		$options = getopt("t:m:l:A:a:");
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

	/** GetRefuelForTruck::__destruct()
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

} new GetRefuelForTruck();
