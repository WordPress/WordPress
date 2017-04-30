<?php
	$flipbooks = get_option("flipbooks");

	if(!$flipbooks){
		$flipbooks = array();
		add_option("flipbooks", $flipbooks);
	}

	function read3d_flipbook_admin_init(){

	}
	add_action("admin_init", "read3d_flipbook_admin_init");
	
	function read3d_flipbook_admin_menu(){
		add_options_page("Real 3D Flipbook Admin", "Real3D Flipbook", "manage_options", "real3d_flipbook_admin", "real3d_flipbook_admin"); 
		add_menu_page("Real 3D Flipbook Admin", "Real3D Flipbook", "manage_options", "real3d_flipbook_admin", "real3d_flipbook_admin",'dashicons-book'); 
	}
	add_action("admin_menu", "read3d_flipbook_admin_menu");
	
	//options page
	function real3d_flipbook_admin()
    {
		$current_action = $current_id = $page_id = '';
		// handle action from url
		if (isset($_GET['action']) ) {
			$current_action = $_GET['action'];
		}

		if (isset($_GET['bookId']) ) {
			$current_id = $_GET['bookId'];
		}
		
		if (isset($_GET['pageId']) ) {
			$page_id = $_GET['pageId'];
		}

		$flipbooks = get_option("flipbooks");
		
		if($flipbooks && $current_id != ''){
			$flipbook = $flipbooks[$current_id];
			if($flipbook){
				$pages = $flipbook["pages"];
			}
		}
		
		switch( $current_action ) {
		
			case 'edit':
				include("edit-flipbook.php");
				break;
				
			case 'delete':
				//delete flipbook with id from url
				unset($flipbooks[$current_id]);
				update_option("flipbooks", $flipbooks);
				include("flipbooks.php");
				break;
				
			case 'delete_all':
				update_option("flipbooks", array());
				include("flipbooks.php");
				break;
				
			case 'duplicate':
				$highest_id = 0;
				foreach ($flipbooks as $flipbook) {
					$flipbook_id = $flipbook["id"];
					if($flipbook_id > $highest_id) {
						$highest_id = $flipbook_id;
					}
				}
				$new_id = $highest_id + 1;
				$flipbooks[$new_id] = $flipbooks[$current_id];
				$flipbooks[$new_id]["id"] = $new_id;
				$flipbooks[$new_id]["name"] = $flipbooks[$current_id]["name"]." (copy)";
				update_option("flipbooks", $flipbooks);
				include("flipbooks.php");
				break;
				
			case 'add_new':
				//generate ID 
				$new_id = 0;
				$highest_id = 0;
				foreach ($flipbooks as $flipbook) {
					$flipbook_id = $flipbook["id"];
					if($flipbook_id > $highest_id) {
						$highest_id = $flipbook_id;
					}
				}
				$current_id = $highest_id + 1;
				//create new book 
				$book = array(	"id" => $current_id, 
								"name" => "flipbook " . $current_id,
								"pages" => array()
						);
				$flipbooks[$current_id] = $book;
				update_option("flipbooks", $flipbooks);
				include("edit-flipbook.php");
				break;
				
			case 'save_settings':
				// trace($flipbooks[$current_id]);
				// trace($_POST);
				$new = array_merge($flipbook, $_POST);
				$flipbooks[$current_id] = $new;
				//reset indexes because of sortable pages can be rearranged
				$oldPages = $flipbooks[$current_id]["pages"];
				$newPages = array();
				$index = 0;
				foreach($oldPages as $p){
					$newPages[$index] = $p;
					$index++;
				}
				$flipbooks[$current_id]["pages"] = $newPages;
				
				
				if(isset($_POST["socialShare"])){
					$oldShare = $flipbooks[$current_id]["socialShare"];
					$newShare = array();
					$index = 0;
					foreach($oldShare as $p){
						$newShare[$index] = $p;
						$index++;
					}
					$flipbooks[$current_id]["socialShare"] = $newShare;
				}else{
					$flipbooks[$current_id]["socialShare"] = array();
				}
				
				//convert values to boolean and integer where needed
				$formatted = array_map("cast", $flipbooks[$current_id]);
				
				$flipbooks[$current_id] = $formatted;
				
				$formatted = array_map("cast", $flipbooks[$current_id]["btnNext"]);
				$flipbooks[$current_id]["btnNext"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnPrev"]);
				$flipbooks[$current_id]["btnPrev"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnZoomIn"]);
				$flipbooks[$current_id]["btnZoomIn"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnZoomOut"]);
				$flipbooks[$current_id]["btnZoomOut"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnToc"]);
				$flipbooks[$current_id]["btnToc"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnThumbs"]);
				$flipbooks[$current_id]["btnThumbs"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnDownloadPages"]);
				$flipbooks[$current_id]["btnDownloadPages"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnDownloadPdf"]);
				$flipbooks[$current_id]["btnDownloadPdf"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnExpand"]);
				$flipbooks[$current_id]["btnExpand"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnExpandLightbox"]);
				$flipbooks[$current_id]["btnExpandLightbox"] = $formatted;
				$formatted = array_map("cast", $flipbooks[$current_id]["btnShare"]);
				$flipbooks[$current_id]["btnShare"] = $formatted;
				
				$formatted = array_map("cast", $flipbooks[$current_id]["deeplinking"]);
				$flipbooks[$current_id]["deeplinking"] = $formatted;

				
				//for each page
				for($i = 0; $i < count($flipbooks[$current_id]["pages"]); $i++){
					$p = $flipbooks[$current_id]["pages"][$i];

					if(isset($p["links"])){
						//reset links 
						$oldLinks = $p["links"];
						if($oldLinks){
							$newLinks = array();
							$index = 0;
							foreach($oldLinks as $lnk){
								$newLinks[$index] = $lnk;
								$index++;
							}
							$flipbooks[$current_id]["pages"][$i]["links"] = $newLinks;
							$p = $flipbooks[$current_id]["pages"][$i];
							//for each link in links
							$formattedLinks = array();
							for($j = 0; $j < count($p["links"]); $j++){
								$l = $p["links"][$j];
								$formattedLink = array_map("cast", $l);
								$formattedLinks[$j] = $formattedLink;
							}
							$flipbooks[$current_id]["pages"][$i]["links"] = $formattedLinks;
						}
					}	
				}
				update_option("flipbooks", $flipbooks);
				include("edit-flipbook.php");
				break;

			default:
				include("flipbooks.php");
				break;
				
		}
    }
	
	function cast($n)
	{
		if($n === "true") {
			return true;
		}else if ($n === "false"){
			return false;
		}else if(is_numeric($n)){
			// return (int)$n;
			return floatval($n);
		}else{
			return $n;
		}
	}