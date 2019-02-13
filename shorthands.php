<?php 

function value(&$var){
	return isset($var)? $var : null;
}
function either(&$var, $else){
	return !empty($var)? $var : $else;
}

function clamp($value, $min, $max){
	return $value>$min ? $value<$max ? $value : $max : $min ;
} 

function isDate($value){
	return preg_match("#^\d{4}-\d{2}-\d{2}$#", $value );
}

?>