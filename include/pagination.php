<?php 
/**
 * Pagination.php
 *
 * The Pagination class is meant to display the no.of pages
 * based on the result rows with the specified limit.
 *
 * Written by: Harsha Vardhan Madiraju a.k.a. Harsha The Developer
 * Last Updated: March 17, 2021
 */

//ini_set("display_errors","on");error_reporting(E_ALL);

 
class Pagination
{
	public $conditions = array();  	// Holds all variables to be posted to target page
	public $targetPage;   			// The Pagename
	public $numres;					// Number of records
	public $methodName;				// Display Variable name
	public $page;					// Nth page of pagination
	public $limit;					// Limit of no of records per page
	public $id;						// Div id to which pagination must be displayed
	public $type;						// Div id to which pagination must be displayed

	/* Class constructor */
	public function __construct($targetPage,$conditions,$numres,$methodName,$page,$limit=50,$id='adminTable',$type=1)
	{
		$this->targetPage=$targetPage;
		$this->conditions=$conditions;
		$this->numres=$numres;
		$this->methodName=$methodName;
		$this->page=$page;
		$this->limit=$limit;
		$this->id=$id;
		$this->type=$type;
	}
	
	public function showPagination()
	{
		$adjacents = 3;
		$total_pages = $this->numres;

		$targetpage = $this->targetPage;					//your file name  (the name of this file)
		$page=$this->page;
		$display=$this->methodName;
		$limit=$this->limit;
		$ids=$this->id;
		$type=$this->type;
		
		$post_conditions='';
		
		$post_conditions=$this->methodName.'=true&limit='.$limit;
		
		if(count($this->conditions)>0)
		{
			foreach($this->conditions as $key=>$value)
			{
				$post_conditions.='&'.$key.'='.$value;
			}
		}
		
		
		


		/* Setup page vars for display. */
		if ($page == 0) $page = 1;                    		//if no page var is given, default to 1.
		$prev = $page - 1;                            		//previous page is page - 1
		$next = $page + 1;                            		//next page is page + 1
		$lastpage = ceil($total_pages/$limit);        		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;                        		//last page minus 1



		/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
		*/
		$pagination = "";
		if($lastpage>1)
		{
			if($type==1)
			{


				$pagination .= "<ul class=\"pagination\">";
				//previous button
				if ($page > 1)
				{

					$pagination.= " <li class=\"page-item\">
					<a class=\"page-link\" onclick=\"setStateGet('".$ids."','".$targetpage."','$post_conditions&page=".$prev."')\">Previous</a></li>";

				}
				else
				{
					$pagination.= "<li class=\"page-item disabled\">
					<a class=\"page-link\">Previous</a></li>";
				}
				//pages
				if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
				{
					for ($counter = 1; $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
						$pagination.= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
						else
						$pagination.= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
					}
				}
				elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
				{
					//close to beginning; only hide later pages
					if($page < 1 + ($adjacents * 2))
					{
						for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
							}
							else
							{
								$pagination.= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}

						$pagination.= "...";

						$pagination.= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions=1&page=".$lpm1."')\">$lpm1</a></li>";

						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$lastpage."')\">$lastpage</a></li>";
					}
					//in middle; hide some front and some back
					elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
					{
						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=1')\">1</a></li>";

						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=2')\">2</a></li>";

						$pagination.= "...";

						for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
							}
							else
							{
								$pagination.= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}

						$pagination.= "...";

						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\">$lpm1</a></li>";

						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$lastpage."')\">$lastpage</a></li>";
					}
					//close to end; only hide early pages
					else
					{
						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('$ids','".$targetpage."','page=1')\">1</a></li>";

						$pagination.= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('$ids','".$targetpage."','page=2')\">2</a></li>";

						$pagination.= "...";

						for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
							}
							else
							{
								$pagination.= "<li class=\"page-item \"><a class=\"page-link\"  onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}
					}
				}

				//next button
				if ($page < $counter - 1)
				{
					$pagination.= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$next."')\">Next</a></li>";
				}
				else
				{
					$pagination.= "<li class=\"page-item disabled\">
					<a class=\"page-link\">Next</a></li>";
					$pagination.= "</ul>\n";
				}

			}
			elseif($type==2)
			{
				$pagination .= "<div class=\"btn-group btn-primary\"><button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
				<ul class=\"dropdown-menu\">";
				
				//pages
				if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
				{
					for ($counter = 1; $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
						$pagination.= "<li class=\"active\"><a class=\"\" >$counter</a></li>";
						else
						$pagination.= "<li class=\" \"><a class=\"\" href=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
					}
				}
				elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
				{
					//close to beginning; only hide later pages
					if($page < 1 + ($adjacents * 2))
					{
						for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"active\"><a class=\"page-link\" >$counter</a></li>";
							}
							else
							{
								$pagination.= "<li class=\"\"><a class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}

						$pagination.= "...";

						$pagination.= "<li class=\"\"><a class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions=1&page=".$lpm1."')\">$lpm1</a></li>";

						$pagination.= "<li class=\"\"><a  class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$lastpage."')\">$lastpage</a></li>";
					}
					//in middle; hide some front and some back
					elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
					{
						$pagination.= "<li class=\"\"><a  class=\"\"  onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=1')\">1</a></li>";

						$pagination.= "<li class=\"\"><a  class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=2')\">2</a></li>";

						$pagination.= "...";

						for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"active\"><a class=\"\" >$counter</a></li>";
							}
							else
							{
								$pagination.= "<li class=\"\"><a  class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}

						$pagination.= "...";

						$pagination.= "<li class=\"\"><a  class=\"\">$lpm1</a></li>";

						$pagination.= "<li class=\"\"><a  class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$lastpage."')\">$lastpage</a></li>";
					}
					//close to end; only hide early pages
					else
					{
						$pagination.= "<li class=\"\"><a  class=\"\" onclick=\"setStateGet('$ids','".$targetpage."','page=1')\">1</a></li>";

						$pagination.= "<li class=\"\"><a  class=\"\"  onclick=\"setStateGet('$ids','".$targetpage."','page=2')\">2</a></li>";

						$pagination.= "...";

						for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
						{
							if ($counter == $page)
							{
								$pagination.= "<li class=\"active\"><a class=\"\" >$counter</a></li>";
							}
							else
							{
								$pagination.= "<li class=\"\"><a class=\"\"  onclick=\"setStateGet('$ids','".$targetpage."','$post_conditions&page=".$counter."')\">$counter</a></li>";
							}
						}
					}
				}

				
				
				$pagination.="</ul></div>";
				
			}
		}


		//echo $targetpage.'?page='.$page.'&limit='.$limit;
		return $pagination;


}

};
 
?>
