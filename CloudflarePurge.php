<?php
	if(function_exists('wfLoadExtension')){
		wfLoadExtension( 'CloudflarePurge' );
	}
	else{
		die( 'This version of of CloudflarePurge requires MediaQiki 1.27+' );
	}
