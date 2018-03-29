<?php
require_once('wfDB.php');
class wfSchema {
	private $deprecatedTables = array(
		'wfBlocks',
		'wfBlocksAdv',
		'wfLockedOut',
		'wfThrottleLog',	
	);
	
	private $tables = array(
"wfBadLeechers" => "(
  `eMin` int(10) unsigned NOT NULL,
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `hits` int(10) unsigned NOT NULL,
  PRIMARY KEY (`eMin`,`IP`)
) DEFAULT CHARSET=utf8",
"wfVulnScanners" => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `ctime` int(10) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  PRIMARY KEY (`IP`)
) DEFAULT CHARSET=utf8",
"wfBlocks7" => "(
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `IP` binary(16) NOT NULL DEFAULT '\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0\\\0',
  `blockedTime` bigint(20) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `lastAttempt` int(10) unsigned DEFAULT '0',
  `blockedHits` int(10) unsigned DEFAULT '0',
  `expiration` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parameters` text,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `IP` (`IP`),
  KEY `expiration` (`expiration`)
) DEFAULT CHARSET=utf8",
"wfConfig" => "(
  `name` varchar(100) NOT NULL,
  `val` longblob,
  `autoload` enum('no','yes') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`name`)
) DEFAULT CHARSET=utf8",
"wfCrawlers" => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `patternSig` binary(16) NOT NULL,
  `status` char(8) NOT NULL,
  `lastUpdate` int(10) unsigned NOT NULL,
  `PTR` varchar(255) DEFAULT '',
  PRIMARY KEY (`IP`,`patternSig`)
) DEFAULT CHARSET=utf8",
"wfFileChanges" => "(
  `filenameHash` char(64) NOT NULL,
  `file` varchar(1000) NOT NULL,
  `md5` char(32) NOT NULL,
  PRIMARY KEY (`filenameHash`)
) CHARSET=utf8",
"wfHits" => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attackLogTime` double(17,6) unsigned NOT NULL,
  `ctime` double(17,6) unsigned NOT NULL,
  `IP` binary(16) DEFAULT NULL,
  `jsRun` tinyint(4) DEFAULT '0',
  `statusCode` int(11) NOT NULL DEFAULT '200',
  `isGoogle` tinyint(4) NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  `newVisit` tinyint(3) unsigned NOT NULL,
  `URL` text,
  `referer` text,
  `UA` text,
  `action` varchar(64) NOT NULL DEFAULT '',
  `actionDescription` text,
  `actionData` text,
  PRIMARY KEY (`id`),
  KEY `k1` (`ctime`),
  KEY `k2` (`IP`,`ctime`),
  KEY `attackLogTime` (`attackLogTime`)
) DEFAULT CHARSET=utf8",
"wfIssues" => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) unsigned NOT NULL,
  `lastUpdated` int(10) unsigned NOT NULL,
  `status` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `severity` tinyint(3) unsigned NOT NULL,
  `ignoreP` char(32) NOT NULL,
  `ignoreC` char(32) NOT NULL,
  `shortMsg` varchar(255) NOT NULL,
  `longMsg` text,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `lastUpdated` (`lastUpdated`),
  KEY `status` (`status`),
  KEY `ignoreP` (`ignoreP`),
  KEY `ignoreC` (`ignoreC`)
) DEFAULT CHARSET=utf8",
"wfPendingIssues" => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) unsigned NOT NULL,
  `lastUpdated` int(10) unsigned NOT NULL,
  `status` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `severity` tinyint(3) unsigned NOT NULL,
  `ignoreP` char(32) NOT NULL,
  `ignoreC` char(32) NOT NULL,
  `shortMsg` varchar(255) NOT NULL,
  `longMsg` text,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `lastUpdated` (`lastUpdated`),
  KEY `status` (`status`),
  KEY `ignoreP` (`ignoreP`),
  KEY `ignoreC` (`ignoreC`)
) DEFAULT CHARSET=utf8",
"wfLeechers" => "(
  `eMin` int(10) unsigned NOT NULL,
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `hits` int(10) unsigned NOT NULL,
  PRIMARY KEY (`eMin`,`IP`)
) DEFAULT CHARSET=utf8",
"wfLocs" => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `ctime` int(10) unsigned NOT NULL,
  `failed` tinyint(3) unsigned NOT NULL,
  `city` varchar(255) DEFAULT '',
  `region` varchar(255) DEFAULT '',
  `countryName` varchar(255) DEFAULT '',
  `countryCode` char(2) DEFAULT '',
  `lat` float(10,7) DEFAULT '0.0000000',
  `lon` float(10,7) DEFAULT '0.0000000',
  PRIMARY KEY (`IP`)
) DEFAULT CHARSET=utf8",
"wfLogins" => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hitID` int(11) DEFAULT NULL,
  `ctime` double(17,6) unsigned NOT NULL,
  `fail` tinyint(3) unsigned NOT NULL,
  `action` varchar(40) NOT NULL,
  `username` varchar(255) NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  `IP` binary(16) DEFAULT NULL,
  `UA` text,
  PRIMARY KEY (`id`),
  KEY `k1` (`IP`,`fail`),
  KEY `hitID` (`hitID`)
) DEFAULT CHARSET=utf8",
"wfReverseCache" => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `host` varchar(255) NOT NULL,
  `lastUpdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`IP`)
) DEFAULT CHARSET=utf8",
"wfScanners" => "(
  `eMin` int(10) unsigned NOT NULL,
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `hits` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`eMin`,`IP`)
) DEFAULT CHARSET=utf8",
"wfStatus" => "(
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ctime` double(17,6) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `type` char(5) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `k1` (`ctime`),
  KEY `k2` (`type`)
) DEFAULT CHARSET=utf8",
'wfNet404s' => "(
  `sig` binary(16) NOT NULL,
  `ctime` int(10) unsigned NOT NULL,
  `URI` varchar(1000) NOT NULL,
  PRIMARY KEY (`sig`),
  KEY `k1` (`ctime`)
) DEFAULT CHARSET=utf8",
'wfHoover' => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` text,
  `host` text,
  `path` text,
  `hostKey` varbinary(124) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `k2` (`hostKey`)
) DEFAULT CHARSET=utf8",
'wfFileMods' => "(
  `filenameMD5` binary(16) NOT NULL,
  `filename` varchar(1000) NOT NULL,
  `knownFile` tinyint(3) unsigned NOT NULL,
  `oldMD5` binary(16) NOT NULL,
  `newMD5` binary(16) NOT NULL,
  `SHAC` binary(32) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `stoppedOnSignature` varchar(255) NOT NULL DEFAULT '',
  `stoppedOnPosition` int(10) unsigned NOT NULL DEFAULT '0',
  `isSafeFile` varchar(1) NOT NULL DEFAULT '?',
  PRIMARY KEY (`filenameMD5`)
) DEFAULT CHARSET=utf8",
'wfBlockedIPLog' => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `countryCode` varchar(2) NOT NULL,
  `blockCount` int(10) unsigned NOT NULL DEFAULT '0',
  `unixday` int(10) unsigned NOT NULL,
  `blockType` varchar(50) NOT NULL DEFAULT 'generic',
  PRIMARY KEY (`IP`,`unixday`,`blockType`)
) DEFAULT CHARSET=utf8",
'wfBlockedCommentLog' => "(
  `IP` binary(16) NOT NULL DEFAULT '\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0\\0',
  `countryCode` varchar(2) NOT NULL,
  `blockCount` int(10) unsigned NOT NULL DEFAULT '0',
  `unixday` int(10) unsigned NOT NULL,
  `blockType` varchar(50) NOT NULL DEFAULT 'gsb',
  PRIMARY KEY (`IP`,`unixday`,`blockType`)
) DEFAULT CHARSET=utf8",
'wfSNIPCache' => "(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IP` varchar(45) NOT NULL DEFAULT '',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `body` varchar(255) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `expiration` (`expiration`),
  KEY `IP` (`IP`),
  KEY `type` (`type`)
) DEFAULT CHARSET=utf8",
'wfKnownFileList' => "(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8",
'wfNotifications' => "(
  `id` varchar(32) NOT NULL DEFAULT '',
  `new` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `category` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '1000',
  `ctime` int(10) unsigned NOT NULL,
  `html` text NOT NULL,
  `links` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;"
/*
'wfPerfLog' => "(
	id int UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	IP int UNSIGNED NOT NULL,
	userID int UNSIGNED NOT NULL,
	UA varchar(1000) NOT NULL,
	URL varchar(1000) NOT NULL,
	ctime int UNSIGNED NOT NULL,
	fetchStart int UNSIGNED NOT NULL,
	domainLookupStart int UNSIGNED NOT NULL,
	domainLookupEnd int UNSIGNED NOT NULL,
	connectStart int UNSIGNED NOT NULL,
	connectEnd int UNSIGNED NOT NULL,
	requestStart int UNSIGNED NOT NULL,
	responseStart int UNSIGNED NOT NULL,
	responseEnd int UNSIGNED NOT NULL,
	domReady int UNSIGNED NOT NULL,
	loaded int UNSIGNED NOT NULL,
	KEY k1(ctime)
) default charset=utf8"
*/
);
	private $db = false;
	public function __construct($dbhost = false, $dbuser = false, $dbpassword = false, $dbname = false){
		$this->db = new wfDB();
	}
	public function dropAll(){
		foreach($this->tables as $table => $def){
			$this->db->queryWrite("drop table if exists " . wfDB::networkTable($table));
		}
		
		foreach ($this->deprecatedTables as $table) {
			$this->db->queryWrite("drop table if exists " . wfDB::networkTable($table));
		}
	}
	public function createAll(){
		foreach($this->tables as $table => $def){
			$this->db->queryWrite("create table IF NOT EXISTS " . wfDB::networkTable($table) . " " . $def);
		}
	}
	public function create($table){
		$this->db->queryWrite("create table IF NOT EXISTS " . wfDB::networkTable($table) . " " . $this->tables[$table]);
	}
	public function drop($table){
		$this->db->queryWrite("drop table if exists " . wfDB::networkTable($table));
	}
}
