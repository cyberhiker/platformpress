<?php
class qbotAdmin extends qbotSettings{

	function run(){
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_dashboard.php';
	}
	
}
?>
