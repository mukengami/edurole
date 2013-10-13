<?php
class password {

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

		if ($this->core->action == "recover") {
			$this->recoverPassword();
		} elseif ($this->core->action == "change" && $core->role > 0) {
			$this->changePassword();
		}
	}

	public function recoverPassword() {
		echo '<div class="menucontainer">
		<div class="menubar">
		<div class="menuhdr"><strong>Home menu</strong></div>
		<div class="menu">
		<a href=".">Home</a>
		<a href="admission/info">Overview of all studies</a>
		<a href="admission">Studies open for intake</a>
		<a href="password">Recover lost password</a>
		</div>
		</div>
		</div>';

		$function = __FUNCTION__;
		$title = 'Recover password';
		$description = 'Recover password your password using your email';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "changepassword.form.php";
	}

	public function changePassword() {
		$function = __FUNCTION__;

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$title = 'Change your account password';
		$description = 'You are able to change your account password here.';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$auth = new auth($this->core);
		
		if (isset($newpass) && isset($oldpass)) {

			if ($newpass == $newpasscheck) {

				if (!$auth->ldapChangePass($this->core->username, $oldpass, $newpass)) {
					$ldap = false;
				}
				if ($auth->mysqlChangePass($this->core->username, $oldpass, $newpass) == false && $ldap == false) {
					$this->core->throwError("The information you have entered is incorrect.");
				}

			} else {
				echo "<h2>The entered passwords do not match</h2>";
			}

		} else {

			echo "<p>Please remember to enter all fields!</p>";
			include $this->core->conf['conf']['formPath'] . "changepass.form.php";

		}
	}
}

?>
