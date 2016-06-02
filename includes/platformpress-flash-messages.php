<?php
if ( $messages = $this->get_flash_messages() ) {
	echo $messages;
	$this->clean_flash_messages('success');
	$this->clean_flash_messages('error');
}
?>