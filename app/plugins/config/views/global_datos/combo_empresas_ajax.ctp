<?php 

if(!empty($values)):
	if(!empty($empty)){
		echo "<option value='' ".(empty($selected) ? "selected" : "")." ></option>";
	}
	foreach($values as $key => $value):
	
		echo "<option value='$key' ".($key == $selected ? "selected" : "")." >$value</option>";
	
	endforeach;

endif;

?>