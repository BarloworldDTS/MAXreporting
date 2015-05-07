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
    CONST SQL_QUERY = "select ca.id, IF(count(tlc.tripLeg_id), 'YES', 'NO') as tripleg, IF(count(db.id), 'YES', 'NO') as is_debriefed, IF(ca.imageGroup_id, 'YES', 'NO') as ocr_images, IF((ca.sysproOrderPlaced && ca.sysproOrderPlacedDate), 'YES', 'NO') as sentToSyspro from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_debrief as db on (db.tripLeg_id=tlc.tripLeg_id) where ca.id=%d;";    
    //: Variables

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
            die("There was no valid cargo ID's provided to run the query. Please provide cargo ID's using the -cargo 'id1,id2' switch.");
        }
        
        $sqlData = new PullDataFromMySQLQuery(self::TENANT_DB, self::HOST_DB);
        // Run query and return result
        if (is_array($_ids)) {
            $_x = 1;
            foreach($_ids as $_id) {
                
                $_query = preg_replace("/%d/", $_id, self::SQL_QUERY);
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
} new runsqlfile();
