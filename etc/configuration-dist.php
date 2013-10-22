<?php
define('APPLICATION_CONFIG') or die;
define('APPLICATION_CONFIG_LOADED', true);
?>
{
	"database" : {
		"host" : "mysqli",
		"host" : "127.0.0.1",
		"port" : null,
		"username" : "",
		"password" : "",
		"name" : "",
		"prefix" : ""
	},
	"site" : {
		"offline" : {
			"message" : "",
			"message-on" : true,
			"image-src" : ""
		},
		"enabled" : true,
		"name" : "Joomla 4",
		"secret" : ""
	},
	"server" : {
		"temp" : ""
	},
	"logging" : {
		"path" : "",
		"handler" : ""
	},
	"session" : {
		"handler" : "database",
		"lifetime" : 15
	},
	"debug" : {
		"enabled" : false,
		"errorLevel" : "maximum",
		"i18n" : false
	},
	"cache" : {
		"enabled" : false,
		"handler" : "",
		"ttl" : 0
	}
}
