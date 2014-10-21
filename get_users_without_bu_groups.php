<?php
// Error reporting
error_reporting ( E_ALL );

// : Includes
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
	protected $_errors;
	
	// : Public functions
	// : Accessors
	
	/**
	 * get_users_without_bu_groups::getError()
	 *
	 * @param string: $this->_errors;
	 */
	public function getError() {
		if (!empty($this->_errors)) {
			return $this->_errors;
		} else {
			return FALSE;
		}
	}
	
	// : End
	// : Setters
	
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
			$_queries = array (
					"select pu.id, p.first_name, p.last_name, p.email, pu.personal_group_id, pu.status from person as p left join permissionuser as pu on (pu.person_id=p.id) where pu.status = 1 order by pu.id asc;",
					"select grl.id, gr.name from group_role_link as grl left join `group` as gr on (gr.id=grl.group_id) where grl.played_by_group_id = %s and grl.group_id IN (%g) order by grl.id asc;",
					"select id, name from `group` where name like 'bu%-%';" 
			);
			
			// Create new SQL Query class object
			$_mysqlQueryMAX = new PullDataFromMySQLQuery ( self::MAXDB );
			$_maxUsersWithNoBU = ( array ) array ();
			$_buGroups = ( array ) array ();
			$_grpList = (string) "";
			$_buGroups = $_mysqlQueryMAX->getDataFromQuery ( $_queries [2] );
			if (! $_buGroups) {
				throw new Exception ( "No business unit groups found in groups table on MAX using the following query:\n{$_queries[2]}" );
			} else {
				foreach ( $_buGroups as $key => $value ) {
					preg_match ( '/^BU.+-.+([Ff]reight|[Dd]edicated|[Mm]anline\s[Mm]ega|[Tt]imber\s24|[Ee]cosse|[Ee]nergy)$/', $value ["name"], $_pregResults );
					if (!empty($_pregResults)) {
							if (empty($_grpList)) 
						{
							$_grpList = $value ["id"];
						} else 
						{
							$_grpList .= ",{$value["id"]}";
						}
					}
				}
			}
			$_totalUsers  = (integer)0;
			$_maxUsers = ( array ) array ();
			$_maxUsers = $_mysqlQueryMAX->getDataFromQuery ( $_queries [0] );
			if ($_maxUsers) {
				foreach ( $_maxUsers as $key => $value ) {
					$_aQuery = preg_replace ( "/%s/", $value ["personal_group_id"], $_queries [1] );
					$_aQuery = preg_replace ( "/%g/", $_grpList, $_aQuery );
					$_result = $_mysqlQueryMAX->getDataFromQuery ( $_aQuery );
					if (empty($_result)) {
						if (! array_key_exists ( $value ["first_name"] . " " . $value ["last_name"], $_maxUsers )) {
							$_maxUsersWithNoBU [$value ["first_name"] . " " . $value ["last_name"]] = $value;
							$_totalUsers++;
						}
					}
				}
			} else {
				throw new Exception ( "No users found in the persons table MAX database using the following query:\n{$_queries[0]}" );
			}
			// Close database connection
			unset ( $_mysqlQueryMAX );
			
			// Return result
			if (empty($_maxUsersWithNoBU)) {
				return FALSE;
			} else {
				return $_maxUsersWithNoBU;
			}
			
		} catch ( Exception $e ) {
			$this->_errors[] = $e->getMessage();
			unset ( $_mysqlQueryMAX );
			return FALSE;
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
	// : End
}
