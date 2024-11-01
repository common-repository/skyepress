<?php 
$reasons = array(
        1 => '<li><label><input type="radio" name="skp_disable_reason" value="temporary"/>' . __('It is only temporary', 'skp-textdomain') . '</label></li>',
		2 => '<li><label><input type="radio" name="skp_disable_reason" value="missing feature"/>' . __('I miss a feature', 'skp-textdomain') . '</label></li>
		<li><textarea name="skp_disable_text[]" placeholder="' . __( 'We\'re continuously developing the plugin. Please describe the feature...', 'skp-textdomain' ) . '"></textarea></li>',
		3 => '<li><label><input type="radio" name="skp_disable_reason" value="technical issue"/>' . __('Technical issue', 'skp-textdomain') . '</label></li>
		<li><textarea name="skp_disable_text[]" placeholder="' . __('Can we help? Please describe your problem...', 'skp-textdomain') . '"></textarea></li>',
		4 => '<li><label><input type="radio" name="skp_disable_reason" value="other plugin"/>' . __('I switched to another plugin', 'skp-textdomain') .  '</label></li>
		<li><input type="text" value="" name="skp_disable_text[]" placeholder="' . __( 'Name of the plugin.', 'skp-textdomain' ) . '"/></li>'
    );
    
    shuffle($reasons);

    $reasons[] = '<li><label><input type="radio" name="skp_disable_reason" value="other"/>' . __('Other reason', 'skp-textdomain') . '</label></li>
                  <li><textarea name="skp_disable_text[]" placeholder="' . __('Please specify, if possible...', 'skp-textdomain') . '"></textarea></li>'

?>


<div id="skp-deactivate-modal" style="display: none;">
    <div id="skp-deactivate-inner">
    	<form action="" method="post">
    	    <h3><strong><?php _e("We're sorry to see you go. Can you please let us know what the problem was?", 'skp-textdomain'); ?></strong></h3>
    	    <ul>
                    <?php 
                    foreach ($reasons as $reason){
                        echo $reason;
                    }
                    ?>
    	    </ul>
    	    <?php if ($email) : ?>
        	    <input type="hidden" name="skp_disable_from" value="<?php echo $email; ?>"/>
    	    <?php endif; ?>
    	    <input id="skp-feedback-submit" class="button button-primary" type="submit" name="skp-feedback-submit" value="<?php _e('Submit & Deactivate', 'skp-textdomain'); ?>"/>
    	    <a id="skp-only-deactivate" class="button"><?php _e("Only Deactivate", 'skp-textdomain'); ?></a>
    	    <a class="skp-deactivate-close" href="#"><?php _e("Don't deactivate", 'skp-textdomain'); ?></a>
    	</form>
    </div>
</div>