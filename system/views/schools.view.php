<?php
class schools {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {

		$this->core = $core;

		$action = $this->core->cleanGet['action'];
		$access = $_SESSION['access'];
		$item = $this->core->cleanGet['item'];

		if (empty($this->core->action) && $access > 2) {

			$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
			$this->listSchools($sql);

		} elseif ($this->core->action == "view") {

			$this->showSchool();

		} elseif ($this->core->action == "edit" && isset($item) && $access > 5) {

			$sql = "SELECT * FROM `schools` WHERE ID = $item";
			$this->editSchool($sql);

		} elseif ($this->core->action == "add" && $access > 5) {

			addSchool();

		} elseif ($this->core->action == "save" && $access > 5) {

			saveSchool();
			$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
			$this->listSchools($sql);

		} elseif ($this->core->action == "delete" && isset($item) && $access > 5) {

			deleteSchool($item);

			$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
			$this->listSchools($sql);
			echo '<script>
                            alert("The school has been deleted");
                    </script>';
		}

	}

	function editSchool($sql) {
		$function = __FUNCTION__;
		$title = 'Edit School';
		$description = 'Remember to save any changes you make';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->formPath . "editschool.form.php";
		}
	}

	function addSchool() {
		$function = __FUNCTION__;
		$title = 'Add School';
		$description = 'Use the following form to create new schools';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->formPath . "addschool.form.php";
	}

	function deleteSchool($id) {

		$sql = 'DELETE FROM `schools`  WHERE `ID` = "' . $id . '"';
		$run = $this->database->doInsertQuery($sql);

	}

	function saveSchool() {

		$item = $this->core->cleanPost['item'];
		$name = $this->core->cleanPost['name'];
		$dean = $this->core->cleanPost['dean'];
		$description = $this->core->cleanPost['description'];

		if (isset($item)) {
			$sql = "UPDATE `schools` SET `Description` = '$description', `Name` = '$name', `Dean` = '$dean' WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `schools` (`ID`, `ParentID`, `Established`, `Name`, `Description`, `Dean`) VALUES (NULL, '0', CURRENT_DATE(), '$name', '$description', '$dean');";
		}

		$run = $this->database->doInsertQuery($sql);

	}

	function listSchools($sql) {
		$function = __FUNCTION__;
		$title = 'Overview of schools';
		$description = 'The following schools currently exist in the system';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><b>Overview of all schools</b>  | <a href="?id=schools&action=add">Add school</a></p><p>
            <table width="768" height="" border="0" cellpadding="3" cellspacing="0">
            <tr class="tableheader">
            <td width="350px"><b>School</b></td>
            <td width="180px"><b>Dean</b></td>
            <td width="170px"><b>Management tools</b></td>
            </tr>';

		$i=0;
		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo '<tr ' . $bgc . '>
                    <td><b><a href="?id=schools&action=view&item=' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>' .
				'<td><a href="?id=view-information&uid=' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>' .
				'<td>
				<a href="?id=schools&action=edit&item=' . $fetch[0] . '"> <img src="templates/default/images/edi.png"> edit</a>
                    <a href="?id=schools&action=delete&item=' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>
                    </td>
                    </tr>';

		}

		echo '</table>
            </p>';
	}

	function showSchool() {

		$item = $this->core->cleanGet['item'];

		$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID AND `schools`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		$i=0;
		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			$function = __FUNCTION__;
			$title = ' . $fetch[3] . ';
			$description = 'The following attributes are saved in the school profile';

			echo component::generateBreadcrumb(get_class(), $function);
			echo component::generateTitle($title, $description);

			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>School name </strong></td>
                    <td>' . $fetch[3] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Dean/Rector of school</strong></td>
                    <td>
                     <a href="?id=view-information&uid=' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Optional description</strong></td>
                    <td>
                            <textarea rows="4" cols="37" name="description">' . $fetch[4] . '</textarea>
                      </td>
                    <td></td>
                  </tr>
                </table>';

		}

	}
}

?>