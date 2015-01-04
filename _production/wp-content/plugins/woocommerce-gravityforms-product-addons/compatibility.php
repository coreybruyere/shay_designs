<?php

if (!function_exists('wc_is_21x')){
	function wc_is_21x(){
		return function_exists('wc_add_notice');
	}
}
