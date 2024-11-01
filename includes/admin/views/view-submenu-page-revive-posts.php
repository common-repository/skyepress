<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php  skp_get_partial( 'plugin-header' ); ?>

<div class="wrap skp-wrap skp-wrap-revive-posts">
    <h1>Schedules <a class="skp-new-schedule-link page-title-action" href="<?php echo add_query_arg( array( 'page' => 'skp-revive-posts', 'subpage' => 'add-schedule' ), admin_url('admin.php') ); ?>">Add New Schedule</a></h1>
    
    <?php 
        $schedules_table = new SKP_List_Table_Schedules;
        $schedules_table->display();
    ?>

</div>