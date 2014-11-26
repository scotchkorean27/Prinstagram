<?php

/*
   this file is full of anti XSS functions
 */

function isUserNameValid($str){
	$pattern = '/^[a-zA-Z0-9]+$/';
	$val = preg_match($pattern, $str);
	if($val === FALSE){
		throw new Exception("you goofs......");
	}
	else{
		return $val === 1 && strlen($str) <= 100;
	}
}

function isGroupNameValid($str){
	return strlen($str) <= 100 && strlen($str) > 0;
}

function isGroupDescValid($str){
	return strlen($str) <= 255 && strlen($str) > 0;
}

function isCommentValid($str){
	return strlen($str) <= 225 && strlen($str) > 0;
}


function isQweetValid($str){
	return strlen($str) <= 225 && strlen($str) > 0;
}


function isCaptionValid($str){
	return strlen($str) <= 100 && strlen($str) > 0;
}

echo isUserNameValid("jlp") ? "t" : "f";

?>

