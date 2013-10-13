<?php
class users{

	public $core;
	
	function __construct($core){
		return $this->core = $core;
	}
	
	public function password($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	public function addUser() {

		$password = password(6);

		if ($this->core->cleanPost["otherdissability"]) {
			$dissabilitytype = $this->core->$cleanPost["otherdissability"];
		}


		// ADDUSER QUERY NEEDS PREPARED STATEMENT

		// Fields user account
		$username = addslashes($this->core->$cleanPost["username"]);
		$firstname = addslashes($this->core->$cleanPost["firstname"]);
		$middlename = addslashes($this->core->$cleanPost["middlename"]);
		$surname = addslashes($this->core->$cleanPost["surname"]);
		$sex = addslashes($this->core->$cleanPost["sex"]);
		$id = addslashes($this->core->$cleanPost["studentid"]);
		$day = addslashes($this->core->$cleanPost["day"]);
		$month = addslashes($this->core->$cleanPost["month"]);
		$year = addslashes($this->core->$cleanPost["year"]);
		$pob = addslashes($this->core->$cleanPost["pob"]);
		$nationality = addslashes($this->core->$cleanPost["nationality"]);
		$streetname = addslashes($this->core->$cleanPost["streetname"]);
		$postalcode = addslashes($this->core->$cleanPost["postalcode"]);
		$town = addslashes($this->core->$cleanPost["town"]);
		$country = addslashes($this->core->$cleanPost["country"]);
		$homephone = addslashes($this->core->$cleanPost["homephone"]);
		$celphone = addslashes($this->core->$cleanPost["celphone"]);
		$dissability = addslashes($this->core->$cleanPost["dissability"]);
		$mstatus = addslashes($this->core->$cleanPost["mstatus"]);
		$email = addslashes($this->core->$cleanPost["email"]);
		$dissabilitytype = addslashes($this->core->$cleanPost["dissabilitytype"]);
		$status = addslashes($this->core->$cleanPost["status"]);
		$roleid = addslashes($this->core->$cleanPost["role"]);

		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$dissabilitytype', '$email', '$mstatus', '$studytype', 'Employed');";

		if ($this->core->database->doInsertQuery($sql)) {

			// Provide new user with access information

			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = $id";

			$dms = $this->core->database->doSelectQuery($sql);

			while ($fetch = mysql_fetch_row($dms)) {

				$passenc = sha1($password);
				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$fetch[4]', '$username', '$roleid', '$passenc');";
				$this->core->database->doInsertQuery($sql);

				echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';

				echo '<div class="successpopup">Username:  <b>' . $username . '</b><br>Password:  <b>' . $password . '</b></div>';

			}

		} else {

			throwError('An error occurred with the information you have entered. Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');

		}

	}

	public function saveEdit() {

		$username = $this->core->cleanPost["username"];
		$firstname = $this->core->cleanPost["firstname"];
		$middlename = $this->core->cleanPost["middlename"];
		$surname = $this->core->cleanPost["surname"];
		$sex = $this->core->cleanPost["sex"];
		$id = $this->core->cleanPost["studentid"];
		$day = $this->core->cleanPost["day"];
		$month = $this->core->cleanPost["month"];
		$year = $this->core->cleanPost["year"];
		$pob = $this->core->cleanPost["pob"];
		$nationality = $this->core->cleanPost["nationality"];
		$streetname = $this->core->cleanPost["streetname"];
		$postalcode = $this->core->cleanPost["postalcode"];
		$town = $this->core->cleanPost["town"];
		$country = $this->core->cleanPost["country"];
		$homephone = $this->core->cleanPost["homephone"];
		$celphone = $this->core->cleanPost["celphone"];
		$dissability = $this->core->cleanPost["dissability"];
		$mstatus = $this->core->cleanPost["mstatus"];
		$email = $this->core->cleanPost["email"];
		$dissabilitytype = $this->core->cleanPost["dissabilitytype"];
		$status = $this->core->cleanPost["status"];
		$roleid = $this->core->cleanPost["role"];

		$sql = "UPDATE `basic-information` SET  `Sex` = '$sex', `Nationality` = '$nationality ', `StreetName` = '$streetname ', `PostalCode` = '$postalcode', `Town` = '$town', `Country` = '$country', `HomePhone` = '$homephone', `MobilePhone` = '$celphone', `Disability` = '$dissability', `DissabilityType` = '$dissabilitytype', `PrivateEmail` = '$email', `MaritalStatus` = '$mstatus', `Status` = '$status' WHERE `ID` = '$id' ";
		$run = $this->core->database->doInsertQuery($sql);

		$sql = "UPDATE `access` SET  `RoleID` =  '$roleid' WHERE `access`.`ID` = '$id';";
		$run = $this->core->database->doInsertQuery($sql);

		return true;
	}
}
?>