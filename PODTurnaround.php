<?php
//: Includes
include_once "FileParser.php";
//: End
/** Object::PODTurnaroundReport
	* @author Feighen Oosterbroek
    * @author feighen@manlinegroup.com
    * @copyright 2011 onwards Manline Group (Pty) Ltd
    * @license GNU GPL
    * @see http://www.gnu.org/copyleft/gpl.html
	*/
class PODTurnaroundReport {
	//: Variables
	protected $_apiurl	= "http://login.max.manline.co.za/api_request/Report/export?";
	protected $_months = array();
	
	//: Public functions
	//: Accessors
	/** PODTurnaroundReport::getApiUrl()
	 * base url to call
	 * @return string
	 */
	protected function getApiUrl() {
		return $this->_apiurl;
	}
	
	/** PODTurnaroundReport::getMonths()
	 * @return array: $this->_months
	 */
	public function getMonths() {
		return $this->_months;
	}
	
	/** PODTurnaroundReport::setMonths(array $months)
	 * @param array $months
	 */
	public function setMonths(array $months) {
		$this->_months = $months;
	}
	//: End
	
	//: Magic
	/** PODTurnaroundReport::__construct()
		* Class constructor
		*/
	public function __construct() {
		$months = (array)array(
				0=>array(date("Y-m-01 00:00", strtotime("-2 months")), date("Y-m-10 00:00", strtotime("-2 months"))),
				1=>array(date("Y-m-10 00:00", strtotime("-2 months")), date("Y-m-20 00:00", strtotime("-2 months"))),
				2=>array(date("Y-m-20 00:00", strtotime("-2 months")), date("Y-m-01 00:00", strtotime("-1 months"))),
				3=>array(date("Y-m-01 00:00", strtotime("-1 months")), date("Y-m-10 00:00", strtotime("-1 months"))),
				4=>array(date("Y-m-10 00:00", strtotime("-1 months")), date("Y-m-20 00:00", strtotime("-1 months"))),
				5=>array(date("Y-m-20 00:00", strtotime("-1 months")), date("Y-m-01 00:00")),
		);
		$this->setMonths($months);
		unset($months);
		$all = (array)array();
		foreach ($this->getMonths() as $val) {
			$report = $this->getApiUrl()."report=76&responseFormat=csv&Start_Date=".$val[0]."&Stop_Date=".$val[1];
			print("<pre>");
			print_r($report);
			print("</pre>".PHP_EOL);
			$fileParser = new FileParser($report);
			$fileParser->setCurlFile("PODTurnaround".date("Ymd", strtotime($val[0])).".csv");
			$data = $fileParser->parseFile();
			$all[] = $data;
		}
	}
	
	/** PODTurnaroundReport::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
}

new PODTurnaroundReport();