<?php
/**
 * config_unistra.php exemple
 */
define("ISGI_API_URL", "http://api.somecds.fr");
define("ISGI_USER", "isgi_username");

/** token used to test api without using ajax **/
define("DEFAULT_TEST_TOKEN", "9587458-98745eh-25mpj");

/** ORIGIN HOST ACCEPTED **/
$authorized_servers = array(
		"http://localhost:8081",
		"http://localhost:8082",
		"http://localhost:8083",
		"http://api.formater",
		"https://api.formater",
		"https://api.poleterresolide.fr",
		"https://en.poleterresolide.fr",
		"https://www.poleterresolide.fr"
);