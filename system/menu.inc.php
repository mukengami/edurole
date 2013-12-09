<?php
class menuConstruct {

	public $core;

	function __construct($core) {
		$this->core = $core;
	}

	public function buildMainMenu($menudata = FALSE) {
	
		if($menudata == FALSE){
			$menu = NULL;
		} else if(isset($this->core->role)){
			$menu = $this->fillMainMenu();
		}
		
		$menu = $this->menuContainer($menu);
		
		return $menu;
	}
	
	public function menuContainer($menu) {
		$container = '<div class="menucontainer">';
		$container .= $menu;
		$container .= '</div><div class="contentpadfull">';
		
		return $container;
	}

	private function fillMainMenu() {

		$menu = NULL;
		
		if($this->core->role < 1000){
			$sql = "SELECT * 
			FROM `permission-link`, `permissions`, `pages`, `page-segment` 
			WHERE `pages`.`PageSegmentID` =  `page-segment`.`ID`
			AND `page-segment`.`SegmentRequiredPermission` = `permission-link`.`ID`
			AND `permission-link`.`PermissionsRangeID` =  `permissions`.`ID` 
			AND `permissions`.`RequiredRoleMin` <= " . $this->core->role . "
			AND " . $this->core->role . " <= `permissions`.`RequiredRoleMax`
			ORDER BY `page-segment`.`SegmentPosition`,  `pages`.`PagePosition`";
			
		}else{
			$sql = "SELECT * 
			FROM `permission-link`, `permissions`, `pages`, `page-segment` 
			WHERE `pages`.`PageSegmentID` =  `page-segment`.`ID`
			AND `page-segment`.`SegmentRequiredPermission` = `permission-link`.`ID`
			AND `permission-link`.`PermissionsRangeID` =  `permissions`.`ID` 
			ORDER BY `page-segment`.`SegmentPosition`,  `pages`.`PagePosition`";
			
		}

		$run = $this->core->database->doSelectQuery($sql);

		$menu .= '<div class="menubar">';
		$menu .= '<div class="menuusr"><strong>' . $this->core->username . '</strong> <i>(' . $this->core->roleName . ')</i> </div>';

		if ($run->num_rows == 0) {
			$menu .= '</div>';
			return $menu;
		}


		$currentSegment = NULL;

		while ($fetch = $run->fetch_assoc()) {

			$segmentName = $fetch['SegmentName'];
			$pageRoute = $fetch['PageRoute'];
			$pageName = $fetch['PageName'];

			if (!isset($currentSegment)) {

				$menu .= $this->segmentHeader($segmentName);

			} else if ($segmentName != $currentSegment) {

				$menu .= '</div>
				<div class="menubar">';

				$menu .= $this->segmentHeader($segmentName);

			}


			if ($segmentName == "Virtual Learning Environment") {

				$study = NULL;
				while ($_SESSION['saobjects']) {
					$program = $fetch[2];

					if ($study != $fetch[1]) {
						$study = $fetch[1];
						$school = $fetch[3];

						$menu .= '<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1"> ' . $school . '</a></div>
							<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1">
								<img src="' . $this->core->fullTemplatePath . '/images/expand.gif"> ' . $study . '</a>
							</div>';
					}

					$menu .= '<div class="menu"><div class="indent"><a href="' . $this->core->conf['conf']['path'] . 'vle&view=school&id=1"><img src="templates/default/images/expand.gif"> ' . $program . '</a></div></div>';
				}

			} 

			if ($pageName == "mail") {

				if ($this->core->conf['conf']['mailEnabled'] == TRUE) {

					$pageName = 'Personal mail <div class="mailcount"><b><img src="'.$this->core->fullTemplatePath .'/images/mail.gif"></b></div>'.
					'<script type="text/javascript">' . "\n" .
					'	jQuery(document).ready(function(){' . "\n" .
					'		url = \''.$this->core->conf['conf']['path'].'/api/mailcount/\';' . "\n".
					'		get_mail(url);' . "\n".
					'	});' . "\n".
					'</script>';
					
					$menu .= $this->pageItem($pageRoute, $pageName);
				}
			} else {
				$menu .= $this->pageItem($pageRoute, $pageName);
			}

			$currentSegment = $segmentName;

		}

		$menu .= '</div>';
		
		return $menu;
	}

	public function segmentHeader($segmentName) {
		$menu = '<div class="menuhdr"><strong>' . $segmentName . '</strong></div>';
		return $menu;
	}

	public function pageItem($pageRoute, $pageName) {
		$menu = '<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></div>';
		return $menu;	
	}

}

?>