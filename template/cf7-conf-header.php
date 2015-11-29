<?php 
global $Custom_pagetitle, $slugs; 

$menu =''; 
$menus = array('history' => __('History',CF7SI_TXT),'settings' => __('Settings',CF7SI_TXT));
$link = menu_page_url($slugs,false);
$addClass = false;
foreach($menus as $menuI => $menuV){
	$class = ''; 
	if(isset($_REQUEST['tab'])){
		if($menuI == $_REQUEST['tab']){ $class = 'nav-tab-active'; }
	} else {
		if(! $addClass){$class = 'nav-tab-active'; $addClass = true;}
		
	}

	$menu .= '<a id="'.$menuI.'" class="nav-tab '.$class.'" href="'.$link.'&tab='.$menuI.'">'.$menuV.'</a>';
}

?>

<div class="wrap">
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper"><?php echo $menu; ?></h2>
	<?php
		if(isset($_REQUEST['tab'])){
			if($_REQUEST['tab'] == 'history'){
				cf7si_history_listing(); 
			} else if($_REQUEST['tab'] == 'settings'){
				CF7SI()->load_files(CF7SI()->get_vars('PATH').'template/cf7-settings.php'); 
			}
		} else {
			cf7si_history_listing(); 
		}
	?>