<?php
class wppb_pagination{
	var $page = 1; // Current Page
	var $perPage = 10; // Items on each page, defaulted to 10
	var $showFirstAndLast = true; // if you would like the first and last page options.

	function generate($total, $perPage = 10, $searchFor, $first, $prev, $next, $last, $currentPage){
		//Assign the page navigation buttons
		$this->first = $first;
		$this->prev = $prev;
		$this->next = $next;
		$this->last = $last;
		$this->implodeBy = NULL; //this variable wasn't set, so it was NULL either way
		
		//Current Page
		$this->currentPage = (int)$currentPage;
	
		//Assign search variable
		if ($searchFor != '')
			$this->searchFor = '&searchFor='.$searchFor;
		else
			$this->searchFor = '';
	
		// Assign the items per page variable
		if (!empty($perPage))
		$this->perPage = $perPage;

		// Assign the page variable
		$this->page = get_query_var ('page');
		if($this->page == 0)
			$this->page = 1;

		// Take the length of the array
		$this->length = $total;

		// Get the number of pages
		$this->pages = ceil($this->length / $this->perPage);

		// Calculate the starting point 
		$this->start  = ceil(($this->page - 1) * $this->perPage);

		// Return the part of the array we have requested
		//return array_slice($array, $this->start, $this->perPage);
		return $this->start;
	}

	function links(){
		// Initiate the links array
		$plinks = array();
		$links = array();
		$slinks = array();

		// Concatenate the get variables to add to the page numbering string
		$queryURL = '';
		if (count($_GET)) {
			foreach ($_GET as $key => $value) {
			  if ($key != 'page')
				if ($key != 'searchFor')
					$queryURL .= '&'.$key.'='.$value;

			}
		}
		
		// If we have more then one pages
		if (($this->pages) > 1){
			// Assign the 'previous page' link into the array if we are not on the first page
			if ($this->page != 1) {
				if ($this->showFirstAndLast) {
				$plinks[] = '<a href="?page=1'.$queryURL.$this->searchFor.'" id="pageLink" class="pageLink_fist">'.$this->first.'</a>';
				}
				$plinks[] = '&nbsp;<a href="?page='.($this->page - 1).$queryURL.$this->searchFor.'" id="pageLink" class="pageLink_previous">'.$this->prev.'</a>&nbsp;';
			}

			// Assign all the page numbers & links to the array
			for ($j = 1; $j < ($this->pages + 1); $j++) {
				if ($this->page == $j) {
					$links[] = ' <a class="selected">'.$j.'</a> '; // If we are on the same page as the current item
				} else {
					$links[] = ' <a href="?page='.$j.$queryURL.$this->searchFor.'" id="pageLink" class="pageLink_'.$j.'">'.$j.'</a> '; // add the link to the array
				}
			}
			
			// Eliminate redundant data (links)
			$elementNo = count($links);

			if ($elementNo > 5){
				$middle = round(($this->currentPage + $elementNo)/2  + 0.5);
				if ($this->currentPage > 3)
					for ($i=0; $i<$this->currentPage - 4; $i++)
						unset ($links[$i]);
						
					if ($this->currentPage > 3)
						$links[$i] = '...';
					
					if ($this->currentPage < $elementNo - 2)
						$links[$this->currentPage + 2] = '...';
					
					
					for ($i=$this->currentPage + 3; $i<$elementNo; $i++){
						if ($i != $middle)
							unset ($links[$i]);
					}
					
					if ($this->currentPage < $elementNo - 3)
						$links[$i] = '...';
			}

			// Assign the 'next page' if we are not on the last page
			if ($this->page < $this->pages) {
				$slinks[] = '&nbsp;<a href="?page='.($this->page + 1).$queryURL.$this->searchFor.'" id="pageLink" class="pageLink_next">'.$this->next.'</a>&nbsp;';
				if ($this->showFirstAndLast) {
					$slinks[] = '<a href="?page='.($this->pages).$queryURL.$this->searchFor.'" id="pageLink" class="pageLink_last">'.$this->last.'</a>';
				}
			}
			
			// Push the array into a string using any some glue
			return implode(' ', $plinks).implode($this->implodeBy, $links).implode(' ', $slinks);
		}
		return;
	}
}
?>