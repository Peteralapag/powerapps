<?php
class WHFunctions
{
	public function GetStockAmount($itemcode,$db)
	{
		$query = "SELECT * FROM rpt_warehouse WHERE item_code='$itemcode'";
		$results = mysqli_query($db, $query); 
		while($ROW = mysqli_fetch_array($results))  
		{
			return $ROW['on_hand'];
		}
	}
	public function ItemClass($class,$db)
	{
		$QUERY = "SELECT * FROM rpt_item_class";
		$pRes = mysqli_query($db, $QUERY);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	$option = '<option value="">--- SELECT CLASS ---</option>';
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				$val = $ROWS['class'];
				$selected = '';
				if($class == $val)
				{
					$selected = 'selected="selected"';
				}
				$option .= '<option '.$selected.' value="'.$val.'">'.$val.'</option>';
			}
			return $option;
		} 
	}
	public function GetItemsDD($db)
	{
		$QUERY = "SELECT * FROM rpt_warehouse";
		$pRes = mysqli_query($db, $QUERY);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	$option = '<option value="">--- SELECT ITEM ---</option>';
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				$val = $ROWS['item_description'];
				$option .= '<option value="'.$val.'">'.$val.'</option>';
			}
			return $option;
		} 
	}
	public function GetUOM($uom,$db)
	{
		$QUERY = "SELECT * FROM rpt_units_measure";
		$pRes = mysqli_query($db, $QUERY);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	$option = '<option value="">--- SELECT UOM ---</option>';
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				$val = $ROWS['unit_name'];
				$selected = '';
				if($uom == $val)
				{
					$selected = 'selected="selected"';
				}
				$option .= '<option '.$selected.' value="'.$val.'">'.$val.'</option>';
			}
			return $option;
		} 
	}

}