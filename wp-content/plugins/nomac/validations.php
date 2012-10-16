<?php
// Simple validation functions

function RequiredIsSet($name) {
	if (!isset($_REQUEST[$name])) {
		return false;
	}
	$_REQUEST[$name] = trim($_REQUEST[$name]);
	if (empty($_REQUEST[$name])) {
		return false;
	}

	return true;
}

function MinLength($name, $length) {
	if (!isset($_REQUEST[$name])) {
		return false;
	}
	$_REQUEST[$name] = trim($_REQUEST[$name]);
	if (strlen($_REQUEST[$name]) < $length) {
		return false;
	}
	return true;
}

?>