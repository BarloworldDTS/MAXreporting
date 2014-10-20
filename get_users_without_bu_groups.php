<?php
// Error reporting
error_reporting ( E_ALL );

// : Includes
require_once dirname ( __FILE__ ) . '/FileParser.php';
// MySQL query pull and return data class
include dirname ( __FILE__ ) . '/PullDataFromMySQLQuery.php';
// : End

/**
 * get_users_without_bu_groups.php
 *
 * @package get_users_without_bu_groups
 * @author Clinton Wright <cwright@bwtsgroup.com>
 * @copyright 2013 onwards Barloworld Transport (Pty) Ltd
 * @license GNU GPL
 * @link http://www.gnu.org/licenses/gpl.html
 *       * This program is free software: you can redistribute it and/or modify
 *       it under the terms of the GNU General Public License as published by
 *       the Free Software Foundation, either version 3 of the License, or
 *       (at your option) any later version.
 *      
 *       This program is distributed in the hope that it will be useful,
 *       but WITHOUT ANY WARRANTY; without even the implied warranty of
 *       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *       GNU General Public License for more details.
 *      
 *       You should have received a copy of the GNU General Public License
 *       along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
class get_users_without_bu_groups {
	// : Constants
	const MAXDB = "max2";
	const T24DB = "application_3";
	
	// : Variables
	protected $_fileName;
	
	// : Public functions
	// : Accessors
	
	// : End
	// : Setters
	
	/**
	 * get_users_without_bu_groups::setExcelFile($_setFile)
	 *
	 * @param string: $_setFile        	
	 */
	public function setExcelFile($_setFile) {
		$this->_excelFileName = $_setFile;
	}
	
	/**
	 * get_users_without_bu_groups::setFileName($_setFile)
	 *
	 * @param string: $_setFile        	
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}
	
	// : End
	
	// : Magic
	/**
	 * get_users_without_bu_groups::__construct()
	 * Class constructor
	 */
	public function __construct() {
		try {
			// Store sql queries
			$_queries = array(
					"select pu.id, p.first_name, p.last_name, p.email, pu.personal_group_id, pu.status from person as p left join permissionuser as pu on (pu.person_id=p.id) where pu.status = 1 order by pu.id asc;",
					"select grl.id, gr.name from group_role_link as grl left join `group` as gr on (gr.id=grl.group_id) where grl.group_id = %s order by grl.id asc;"
			);
			
			// Create new SQL Query class object
			$_mysqlQueryMAX = new PullDataFromMySQLQuery ( self::MAXDB );
			$_maxUsersWithNoBU = (array) array();
			$_maxUsers = (array) array(); 
			$_maxUsers = $_mysqlQueryMAX->getDataFromQuery($_queries[0]);
			if ($_maxUsers) {
				foreach($_maxUsers as $key => $value) {
					$_aQuery = preg_replace("/%s/", $value["personal_group_id"], $_queries[1]);
					$_result = $_mysqlQueryMAX->getDataFromQuery($_aQuery);
					if (count($_result) === 0) {
						if (!array_key_exists($value["first_name"] . " " . $value["last_name"], $_maxUsers)) {
							$_maxUsersWithNoBU[$value["first_name"] . " " . $value["last_name"]] = $value;
						}
					}
				}
			}
			var_dump($_maxUsersWithNoBU);
			unset($_mysqlQueryMAX);
			
		} catch ( Exception $e ) {
			echo "Caught exception: ", $e->getMessage (), "\n";
			echo "F", "\n";
			exit ();
		}
	}
	
	/**
	 * get_users_without_bu_groups::__destruct()
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

new get_users_without_bu_groups ();
