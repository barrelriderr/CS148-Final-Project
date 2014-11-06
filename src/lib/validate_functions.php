<?php

function verifyUsername($testString) {
	return (preg_match ("/^([[:alnum:]]|-|\.|_|')+$/", $testString));
}