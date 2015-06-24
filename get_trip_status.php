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
    CONST SQL_QUERY = "SELECT
ca.id,
ca.tripNumber,
tl.offloadingArrivalTime,
count(tlc.tripLeg_id) AS triplegs,
IF(db.debriefStartDate, 'YES', 'NO') AS is_debriefed,
IF(ca.imageGroup_id, 'YES', 'NO') AS ocr_images,
IF((ca.sysproOrderPlaced && ca.sysproOrderPlacedDate), 'YES', 'NO') AS sentToSyspro,
IF((ca.companyInvoiceNumber), 'YES', 'NO') AS isInvoiced,
(CASE
WHEN (count(tlc.tripLeg_id) > 0)
THEN (IF((tl.offloadingArrivalTime || db.debriefStartDate), 'NO', 'YES'))
ELSE
NULL
END)
as triplegDeleteable,
(CASE
WHEN (count(tlc.tripLeg_id) > 0)
THEN (IF((tl.loadingArrivalTime && tl.loadingStarted && tl.loadingFinished && tl.timeLeft && tl.offloadingArrivalTime && tl.offloadingStarted && tl.offloadingCompleted && tl.kmsBegin && tl.kmsEnd), 'YES', 'NO'))
ELSE
NULL
END)
AS tripLegCompleted,
(IF(!(ca.imageGroup_id = NULL && ca.companyInvoiceNumber = NULL && db.debriefStartDate = NULL && tl.offloadingArrivalTime = NULL), 'YES', 'NO')) AS isManuallyDeleteable,
(IF((!ca.imageGroup_id && !db.debriefStartDate && !count(tlc.tripLeg_id)), 'YES', 'NO')) AS isDeleteableByScript
FROM udo_cargo AS ca
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.cargo_id=ca.id)
LEFT JOIN udo_tripleg AS tl ON (tl.id=tlc.tripLeg_id)
LEFT JOIN udo_debrief AS db ON (db.tripLeg_id=tlc.tripLeg_id)
WHERE ca.id=%d;";
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
