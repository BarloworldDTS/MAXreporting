<?php
// Error reporting
error_reporting ( E_ALL );

// : Includes
require_once 'get_users_without_bu_groups.php';
require_once 'Archive/FileParser.php';
// : End

/**
 * get_users_bu_from_list.php
 *
 * @package get_users_bu_from_list
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
class get_users_bu_from_list {
	const DS = DIRECTORY_SEPARATOR;
	const DELIMITER = ',';
	const ENCLOSURE = '"';
	const CSV_LIMIT = 0;
	
	// : Variables
	protected $_fileName;
	protected $_errors;
	
	// : Public functions
	// : Accessors
	
	/**
	 * get_users_bu_from_list::getError()
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
	
	/**
	 * get_users_bu_from_list::stringHypenFix($_value)
	 * Replace long hyphens in string to short hyphens as part of a problem
	 * created when importing data from spreadsheets
	 *
	 * @param string: $_value
	 * @param string: $_result
	 */
	public function stringHypenFix($_value) {
		$_result = preg_replace ( "/–/", "-", $_value );
		return $_result;
	}
	
	// : End
	// : Setters
	
	/**
	 * get_users_bu_from_list::setFileName($_setFile)
	 *
	 * @param string: $_setFile        	
	 */
	public function setFileName($_setFile) {
		$this->_fileName = $_setFile;
	}
	
	// : End
	
	// : Magic
	/**
	 * get_users_bu_from_list::__construct()
	 * Class constructor
	 */
	public function __construct($_file1, $_file2) {
		try {
			// : using the filename given use the sub dir 'Data' of the script root path
			$_fullPath1 = dirname(__FILE__) . self::DS . "Data" . self::DS . $_file1;
			$_fullPath2 = dirname(__FILE__) . self::DS . "Data" . self::DS . $_file2;
			
			// Run query and get all users that do not belong to business unit groups and export results to a CSV file
			$_maxusers = new get_users_without_bu_groups($_fullPath1);
			
			// : Check if the above exported CSV file exists and import the data
			if (file_exists($_fullPath1)) {
				$_csvdata1 = $this->ImportFromCSV($_fullPath1);
			}
			// : End
			
			// : Import data from CSV file containing list of employees at BWT
			if (file_exists($_fullPath2)) {
				$_csvdata2 = $this->ImportFromCSV($_fullPath2);  
			}
			// : End
			
			// Destroy object _maxusers
			unset($_maxusers);
			
			// : If both csv files imported and contain data then process the data
			if ((isset($_csvdata1)) && (!empty($_csvdata1)) && (!empty($_csvdata2)) && (isset($_csvdata2))) {
				foreach($_csvdata1 as $key1 => $value1) {
					foreach($_csvdata2 as $key2 => $value2) {
						
					}
				}
			}
			// : End
			
		} catch ( Exception $e ) {
			$this->_errors[] = $e->getMessage();
			unset ( $_mysqlQueryMAX );
			return FALSE;
		}
	}
	
	/**
	 * get_users_bu_from_list::__destruct()
	 * Class destructor
	 * Allow for garbage collection
	 */
	public function __destruct() {
		unset ( $this );
	}
	// : End
	
	// : Private Functions
	
	/**
	 * get_users_bu_from_list::ImportFromCSV($csvFile)
	 * From supplied csv file save data into multidimensional array
	 *
	 * @param string: $csvFile
	 * @param array: $_result
	 */
	private function ImportFromCSV($csvFile) {
		try {
			$_data = ( array ) array ();
			$_header = NULL;
			if (file_exists ( $csvFile )) {
				if (($_handle = fopen ( $csvFile, 'r' )) !== FALSE) {
					while ( ($_row = fgetcsv ( $_handle, self::CSV_LIMIT, self::DELIMITER, self::ENCLOSURE )) !== FALSE ) {
						if (! $_header) {
							foreach ( $_row as $_value ) {
								$_header [] = strtolower ( $_value );
							}
						} else {
							$_data [] = array_combine ( $_header, $_row );
						}
					}
					fclose ( $_handle );
						
					if (count ( $_data ) != 0) {
	
						foreach ( $_data as $_key => $_value ) {
							foreach ( $_value as $_keyA => $_valueA ) {
								$_data [$_key] [$_keyA] = $this->stringHypenFix ( $_valueA );
							}
						}
	
						return $_data;
					} else {
						$_msg = preg_replace ( "@%s@", $csvFile, self::FILE_EMPTY );
						throw new Exception ( $_msg );
					}
				} else {
					$_msg = preg_replace ( "@%s@", $csvFile, self::COULD_NOT_OPEN_FILE );
					throw new Exception ( $_msg );
				}
			} else {
				$_msg = preg_replace ( "@%s@", $csvFile, self::FILE_NOT_FOUND );
				throw new Exception ( $_msg );
			}
		} catch ( Exception $e ) {
			$this->_functionError = $e->getMessage ();
			return FALSE;
		}
	}
	// : End
}
new get_users_bu_from_list("test.csv","userlist.csv");
