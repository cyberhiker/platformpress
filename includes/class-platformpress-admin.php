<?php
class platformpressAdmin extends platformpressSettings{

	function run(){
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_dashboard.php';
	}
	
}
?>
