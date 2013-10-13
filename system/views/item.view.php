<?php
class item {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		$item = $this->core->cleanGet['item'];

		if (empty($this->core->action)) {

			$function = __FUNCTION__;
			$title = 'News';
			$description = 'The following stories are available';

			echo $this->core->breadcrumb->generate(get_class(), $function);
			echo component::generateTitle($title, $description);

			$this->showItem($item);
			$this->showNewsOverview();

		} elseif ($this->core->action == "edit" && $this->core->role > 102) {

			$this->edit($item);

		} elseif ($this->core->action == "save" && $this->core->role > 102) {

			$this->editsave($item);

		}
	}

	function showNewsOverview() {

		$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'news'";
		$run = $this->core->database->doInsertQuery($sql);

		echo '<div class="newscontainers">	<h2>News and updates</h2> <p>';

		while ($row = $run->fetch_row()) {
			echo ' <li> <b><a href="' . $this->core->conf['conf']['path'] . 'item/' . $row[0] . '">' . $row[1] . '</a></b></li>';
		}

		echo '</p></div>';
	}


	function showItem($id) {

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="welcomecontainers">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . 'item/edit/' . $id . '">edit</a></div>';
		}

		while ($row = $run->fetch_row()) {
			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';
		}

		echo '</div>';

	}

	function edit($id) {
		$function = __FUNCTION__;
		$title = 'Edit item';
		$description = 'Please remember to save your changes';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";

		$run = $this->core->database->doSelectQuery($sql);

		if ($this->core->role == 1000) {
			echo "<script type=\"text/javascript\">
				Aloha.ready( function() {
					var $ = Aloha.jQuery;
					$('.editable').aloha();
				});
			</script>";
		}

		while ($row = $run->fetch_row()) {
			echo ' <p class="title2">Editing News Item</p> <p><b>Remember to click save</b><form name="form1" method="post" action="mo.php/save&atat="><input type=hidden name=filename value=>
			<input name="itemname" class="editable" value="' . $row[1] . '"> <br> <textarea name="item" rows="30" cols="105" class="editable">
			' . $row[2] . '</textarea>';
		}

		echo '</div>';

	}
}

?>

