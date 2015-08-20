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
    CONST SQL_QUERY = "select
ca.id,
ca.tripNumber,
ca.orderNumber,
ca.companyInvoiceNumber,
cu.tradingName,
t.id as truck_id,
t.fleetnum,
pc.name as product,
ca.tonsActual,
DATE_ADD(ca.plannedLoadingArrivalDate, INTERVAL 2 HOUR) as plannedLoadingArrivalDate,
DATE_ADD(ca.plannedOffloadingArrivalDate, INTERVAL 2 HOUR) as plannedOffloadingArrivalDate,
cf.name as cityFrom,
lf.name as townFrom,
ct.name as cityTo,
lt.name as townTo,
rt.name as rateType,
dr.nickname as driver,
DATE_ADD(tl.loadingArrivalETA, INTERVAL 2 HOUR) as loadingArrivalETA,
DATE_ADD(tl.offloadingArrivalETA, INTERVAL 2 HOUR) as offloadingArrivalETA,
DATE_ADD(tl.loadingArrivalTime, INTERVAL 2 HOUR) as loadingArrivalTime,
DATE_ADD(tl.loadingStarted, INTERVAL 2 HOUR) as loadingStarted,
DATE_ADD(tl.loadingFinished, INTERVAL 2 HOUR) as loadingFinished,
DATE_ADD(tl.timeLeft, INTERVAL 2 HOUR) as timeLeft,
DATE_ADD(tl.offloadingArrivalTime, INTERVAL 2 HOUR) as offloadingArrivalTime,
DATE_ADD(tl.offloadingStarted, INTERVAL 2 HOUR) as offloadingStarted,
DATE_ADD(tl.offloadingCompleted, INTERVAL 2 HOUR) as offloadingCompleted,
tl.kmsBegin,
tl.kmsEnd,
tl.subcontractor_id,
ca.sysproError,
ca.sysproOrderPlaced,
ca.sysproOrderPlacedDate,
ca.rate_id,
ca.truckDescription_id,
ca.tripCaptureCompleted,
ra.businessUnit_id as rateBU,
f.name as fleetOnPB
from udo_cargo as ca
left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id)
left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id)
left join udo_truck as t on (t.id=tl.truck_id)
left join udo_fleettrucklink as ftl on (ftl.truck_id=t.id)
left join udo_fleet as f on (f.id=ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
left join udo_productcategory as pc on (pc.id=ca.productCategory_id)
left join udo_location as lf on (lf.id=ca.locationFrom_id)
left join udo_location as lt on (lt.id=ca.locationTo_id)
left join udo_location as cf on (cf.id=ca.cityFrom_id)
left join udo_location as ct on (ct.id=cityTo_id)
left join udo_rates as ra on (ra.id=ca.rate_id)
left join udo_ratetype as rt on (rt.id=ra.rateType_id)
left join udo_driver as dr on (dr.id=tl.driver_id)
left join udo_customer as cu on (cu.id=ca.customer_id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND
ca.id = %d 
group by ca.id";
	//: Variables
        private static $_usage = array(
                "get_trip_info - Get info for a MAX Trip",
                "",
                "Usage: get_trip_info.php -c cargo_id",
                "",
                "Arguments:",
                "",
                "Required options:",
                "-c: CargoID",
                "",
                "Example:",
                "",
                "get_trip_info.php -c 900100",
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
