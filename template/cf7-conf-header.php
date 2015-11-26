<?php global $Custom_pagetitle, $slugs; ?>
<?php
//$link = menu_page_url($slugs,false);
//$menu = '<a class="page-title-action" href="'.$link.'&history-view=true">View History</a>';
//if(isset($_REQUEST['history-view'])){
//	$menu = '<a class="page-title-action" href="'.$link.'">Settings</a>';
//}
?>

<div class="wrap">
	<?php cf7si_history_listing(); ?>
	