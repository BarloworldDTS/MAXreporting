#!/usr/bin/php
<?php
//: Includes
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

/***********************************************************************
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
 *
 ***********************************************************************
*/

class GetRefuelForTruck {

    // : Constants
    CONST TENANT_DB = "max2";
    CONST HOST_DB = "192.168.1.19";
    CONST END_DATE_STR = " 00:00:00";
    CONST SQL_LAST = "ORDER BY r.fillDateTime DESC LIMIT 1";
    CONST SQL_ALL = "ORDER BY r.fillDateTime ASC";
    CONST SQL_QUERY_MAIN = "SELECT r.id AS 'Refuel ID',
    ron.orderNumber AS 'Refuel Order Number',
    DATE_ADD(r.fillDateTime, INTERVAL 2 HOUR) AS 'Refuel DateTime',
    l.name AS 'Refuel Point',
    authorized AS 'Refuel Point Status',
    t.fleetnum AS 'Truck',
    CONCAT(pr.first_name, ' ', pr.last_name) AS 'Driver Fullnames',
    r.odo AS 'Odometer Reading',
    r.litres AS 'Litres',
    r.cost AS 'Cost',
    CONCAT(pc.first_name, ' ', pc.last_name) AS 'Created By',
    r.time_created AS 'Time Created',
    CONCAT(pm.first_name, ' ', pm.last_name) AS 'Last Modified By',
    r.time_last_modified AS 'Time Last Modified'
    FROM udo_refuel AS r
    LEFT JOIN udo_refuelordernumber AS ron ON (ron.id = r.refuelOrderNumber_id)
    LEFT JOIN udo_truck AS t ON (t.id = r.truck_id)
    LEFT JOIN udo_driver AS dr ON (dr.id = r.driver_id)
    LEFT JOIN person AS pr ON (pr.id = dr.person_id)
    LEFT JOIN udo_point AS pt ON (pt.id = r.point_id)
    LEFT JOIN udo_location AS l ON (l.id = pt._udo_Location_id)
    LEFT JOIN permissionuser AS puc ON (puc.id = r.created_by)
    LEFT JOIN permissionuser AS pum ON (pum.id = r.last_modified_by)
    LEFT JOIN person AS pc ON (pc.id = puc.person_id)
    LEFT JOIN person AS pm ON (pm.id = pum.person_id)
    AND r.odo %odo% NULL
    AND t.fleetnum LIKE '%truck%'
    %end%";
    // : End
    
    //: Variables
    protected $sqlQuery;
    protected $trucks;
    private static $_usage = array(
        "GetRefuelForTruck - Get refuel orders for a month for one or more trucks from MAX",
	    "",
        "Usage: GetRefuelForTruck.php -t fleetnum1,fleetnum2 -m 2016-02",
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
        "-a: [All|Last[,Complete]]",
        "-a All                     (Fetch all refuels done by the truck(s) for the month",
        "                           including incompleted refuels - this is the default)",
        "",
        "-a All,Complete            (Fetch all refuels done by the truck(s) for the month",
        "                           only inclusive of completed refuels)",
        "",
        "-a Last,Complete           (Fetch the last completed refuel for each truck for the month",
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
        "GetRefuelForTruck.php -t fleetnum1,fleetnum2 -m 2016-04 -a Last,Complete",
        "",
        "This will fetch the last refuel order done within the month of '2016-04' for trucks",
        "fleetnum1 and fleetnum2",
        ""
    );

	//: Public Methods
	
	//: Getters
    /**
	 * GetRefuelForTruck::getSqlQuery()
	 * Get the SQL Query that was built during the run of this script
	 *
	 * @param return mixed
	 */
	public function getSqlQuery()
	{
	    if ($this->sqlQuery && is_string($this->sqlQuery)) {
	        return $this->sqlQuery;
	    }
	    return FALSE;
	}
	
	/**
	 * GetRefuelForTruck::getTrucks()
	 * Get the trucks that were passed to the script
	 * 
	 * @param return mixed
	 */
    public function getTrucks()
	{
	    if ($this->getTrucks && is_array($this->getTrucks)) {
	        return $this->getTrucks;
	    }
	    return FALSE;
	}
	//: End
	
	//: Setters
	//: End

	//: Magic
	
	/** 
	 * GetRefuelForTruck::__construct()
	 * Class constructor
	 */
	public function __construct() {
	
	// Construct an array with predefined date(s) which we will use to run a report
	    $options = getopt("t:m:a:");
	    $sqlQuery = self::SQL_QUERY_MAIN;
        $monthDate = date("Y-m");
        $trucks = (array) array();
        
        // : Check for required options
        if (array_key_exists('t', $options)) {
            $trucks = explode(',', $options['t']);
        } else {
		    $this->printUsage("-t switch argument is required. Refer to usage below");
        }
        // : End
        
        // : Fetch month for which to fetch the refuels
        if (array_key_exists('m', $options)) {
        
            $matches = array();
            $opt = $options["m"];
            
            // Check value given for month matches the correct expected date format
            if (preg_match('/^[1-9][0-9]{3}-[0-9]{2}$/', $opt, $matches)) {
                $monthDate = $opt;
            } else {
                $this->printUsage("Value given for month is invalid. It must in the date format: YYYY-MM, e.g. " . strval(date("Y-m")));
            }
            
        }
        
        // Finalise the string value of the start and stop date based on the determined month
        $startDate = date("$monthDate-01" . self::END_DATE_STR);
        $stopDate = date("$monthDate-t" . self::END_DATE_STR);
        
        // : End
        
        // : Make check for the presence of exclusive optional arguments

        if (array_key_exists('a', $options)) {
        
            $opt = strtolower($options['a']);
            
            preg_match('/complete/i', $opt, $matches);
            if ($matches) {
                preg_replace('/%odo%/', "IS NOT", $sqlQuery);
            } else {
                preg_replace('/%odo%/', "IS", $sqlQuery);
            }
            
            // : SQL query amendment based on action selected
            
            unset($matches);
            preg_match('/all|last/', $opt, $matches);
            
            if ($matches) {
            
                $match = $matches[0];
                
                if ($match && is_string($match)) {
                
                    switch ($match) {
                        case 'last': {
                            preg_replace('/%end%/', self::SQL_LAST, $sqlQuery);
                            break;
                        }
                        case 'all':
                        default: {
                            preg_replace('/%end%/', self::SQL_ALL, $sqlQuery);
                        }
                    }
                    
                    // Set object property `sqlQuery` to the built query, as it is now complete
                    $this->sqlQuery = $sqlQuery;
                                    
                } else {
                    $this->printUsage('Could not find a valid operation mode. Please see usage below:');
                }
            }
            // : End
            
        } else {
            $this->printUsage('Operation mode not given. Using default: fetch all including uncompleted refuels [All]');
        }
        // : End

        
        $sqlData = new PullDataFromMySQLQuery(self::TENANT_DB, self::HOST_DB);
        // Run query and return result
        if (is_array($trucks) && count($trucks) > 0) {
        
            $this->trucks = $trucks;
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
