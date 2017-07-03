<?php 
$slug =  $cf7_isms_plugin_data['addon_slug']; 
$wrapperClass = 'plugin-card plugin-card-'.$slug.' wc-pbp-addon-all wc-pbp-addon-'.$cf7_isms_plugin_data['category-slug'];
if($cf7_isms_plugin_data['is_active']){
    $wrapperClass .= ' wc-pbp-addon-active';
} else {
    $wrapperClass .= ' wc-pbp-addon-inactive';
}

?>
<div id="<?php echo $slug; ?>" class="<?php echo $wrapperClass; ?>">
	<?php cf7_isms_get_ajax_overlay(); ?>
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3> 
			   <?php echo $cf7_isms_plugin_data['Name']; ?> 
			   [<small><?php _e('V',CF7_ISMS_TXT);?> <?php echo $cf7_isms_plugin_data['Version']; ?></small>] 
			   <?php $this->get_addon_icon($cf7_isms_plugin_data); ?>
			</h3>
		</div>
		<div class="desc column-description">
			<p><?php echo $cf7_isms_plugin_data['Description']; ?></p>
			<p class="authors">
				
				<cite>
					<?php _e('By',CF7_ISMS_TXT); ?> 
					<a href="<?php echo $cf7_isms_plugin_data['AuthorURI']; ?>"> <?php echo $cf7_isms_plugin_data['Author']; ?></a> 
				</cite> 
			</p>
		</div>
	</div>
	<div class="plugin-card-top wc-pbp-addons-required-plugins">
		<?php if(!empty($required_plugins)): ?>
			<div>
				<h3><?php _e('Required Plugins :',CF7_ISMS_TXT); ?></h3>
				<ul>
					<?php
						$echo = '';
						foreach($required_plugins as $plugin){
							$plugin_status = $this->check_plugin_status($plugin['Slug']);
							$status_val = __('InActive',CF7_ISMS_TXT);
							$class = 'deactivated';
							if($plugin_status === 'notexist'){ $status_val = __('Plugin Dose Not Exist',CF7_ISMS_TXT); $class = 'notexist'; } 
							else if($plugin_status === true){ $status_val = __('Active',CF7_ISMS_TXT); $class = 'active'; }
							if(!isset($plugin['Version'])){$plugin['version'] = '';}
							echo '<li class="'.$class.'">';
							
								echo '<span class="cf7_isms_required_addon_plugin_name"> <a href="'.$plugin['URL'].'" > '.
									$plugin['Name'].' ['.$plugin['Version'].'] </a> </span> : ';
								echo '<span class="cf7_isms_required_addon_plugin_status '.$class.'">'.$status_val.'</span>';
							echo '</li>';
							unset($plugin_status);
						}
					?>
				</ul>
				<p> <span><?php _e('Above Mentioned Plugin name with version are Tested Upto',CF7_ISMS_TXT);?></span> </p>
				<small><strong><?php _e('Addon Slug : ',CF7_ISMS_TXT); ?></strong><?php echo $cf7_isms_plugin_slug;?></small>
			</div>
		<?php endif; ?>
	</div>
	<div class="plugin-card-bottom">
		<div class="column-updated" data-pluginslug="<?php echo $slug; ?>">
			<?php echo $this->get_addon_action_button($cf7_isms_plugin_slug,$required_plugins); ?>
		</div>
		<div class="column-downloaded"><strong><?php _e('Last Updated:',CF7_ISMS_TXT);?></strong>
			<span title="<?php echo $cf7_isms_plugin_data['last_update']; ?>"><?php echo $cf7_isms_plugin_data['last_update']; ?></span>
		</div>
		<div class="column-downloaded cf7_isms_ajax_response"></div>
	</div>
</div>