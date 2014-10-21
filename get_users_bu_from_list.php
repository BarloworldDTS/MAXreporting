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
	public function __construct($_file) {
		try {
			$_fullPath = dirname(__FILE__) . self::DS . "Data" . self::DS . $_file;

			if (file_exists($_fullPath)) {
				$_csvdata = $this->ImportFromCSV($_fullPath);  
			}
			
			$_db_users = new get_users_without_bu_groups;
			
			if ((isset($_db_users)) && (!empty($_db_users)) && (!empty($_csvdata)) && (isset($_csvdata))) {
				foreach($_db_users as $key => $value) {
					$_name = strtolower($_value["first_name"]);
					$_found = array();
					foreach($_csvdata as $aKey => $aValue) {
						$_result = preg_grep("/^" . $_name . "*$/", $aValue);
						if ($_result) {
							$_found[] = $_result;
						}
					}
					if ($_found) {
						var_dump($_found);
					}
				}
			}
			
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
new get_users_bu_from_list("userlist.csv");
