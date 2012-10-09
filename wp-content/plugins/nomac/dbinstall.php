<?php
require_once("include.php");

function createImageCycleTable() {
	global $wpdb;
	$wpdb->show_errors();

	$tablename = $wpdb->prefix . TABLE_IMAGECYCLE;
	$sql = "CREATE TABLE IF NOT EXISTS ". $tablename . " (
                id bigint(20) unsigned NOT NULL auto_increment,
                title varchar(200) NOT NULL default '',
                image_url text NOT NULL,
                link_url text NULL,
                PRIMARY KEY  (id))
		;";
	create_table($tablename, $sql, "nomac_imagecycle_version", "1.1");

	$wpdb->hide_errors();
}


function createRulechangeTable() {
	global $wpdb;
	$wpdb->show_errors();	

	$tablename = $wpdb->prefix . TABLE_RULECHANGE;
	$sql = "CREATE TABLE " . $tablename . " (
			Id bigint(20) unsigned not null auto_increment,
			Year YEAR(4) not null,
			SubmittedByIp varchar(64) not null,
			SubmittedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			SubmittedBy varchar(200) not null,
			SubmitterEmail varchar(200) not null,
			Class varchar(10) not null,
			Page varchar(20) NULL,
			Article varchar(20) NULL,
			OldText MEDIUMTEXT,
			NewText MEDIUMTEXT,
			Comment MEDIUMTEXT,
			PRIMARY KEY  (Id)
		);";
	create_table($tablename, $sql, "nomac_rulechange_version", "1.0");

	$wpdb->hide_errors();
}


function createLicensingTable() {
	global $wpdb;
	$wpdb->show_errors();	

	
	$tablename = $wpdb->prefix . TABLE_LICENSING;
	$sql = "CREATE TABLE " . $tablename . " (
			Id bigint(20) unsigned not null auto_increment,
			Voornaam varchar(100) not null,
			Achternaam varchar(100) not null,
			Straat varchar(100) not null,
			HuisNr varchar(10) not null,
			PostCode varchar(7) not null,
			Woonplaats varchar(100) not null,
			GeboorteDatum DATE not null,
			TelefoonNr varchar(15) null,
			Email varchar(100) null,
			LidBijClub varchar(100) not null,
			Freq1 varchar(20) not null,
			Freq2 varchar(20) null,
			Freq3 varchar(20) null,
			Transponder varchar(10) not null,
			VorigeLicentieNr varchar(10) not null,
			Klasse varchar(10) not null,
			Bedrag int not null,
			Status varchar(20) not null,
			Year YEAR(4) not null,
			RegistrationDate datetime not null,
			foto mediumblob not null,
			fotoContentType varchar(50) not null,
			PRIMARY KEY  (Id)
		);";
	create_table($tablename, $sql, "licensing_version", "0.5.1");


	$tablename = $wpdb->prefix . TABLE_FREQUENCY;
	$sql = "CREATE TABLE IF " . $tablename . " (
			Id int unsigned not null auto_increment,
			Code varchar(10) not null,
			Name varchar(150) not null,
			PRIMARY KEY  (Id),
			UNIQUE KEY Code_Unique (Code)
		);";
	create_table($tablename, $sql, "licensing_version", "0.5.2");
	if ($wpdb->get_var("SELECT COUNT(Code) FROM $tablename") == 0) {
                $data = array();

		$data["Name"] = "Spectrum";
		$data["Code"] = "2.4Ghz";
		$wpdb->insert($tablename, $data);
		$wpdb->insert($tablename, $data);
		$data["Name"] = "26.995MHz";
		$data["Code"] = "26.995MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "27.045MHz";
		$data["Code"] = "27.045MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "27.095MHz";
		$data["Code"] = "27.095MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "27.145MHz";
		$data["Code"] = "27.145MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "27.195MHz";
		$data["Code"] = "27.195MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.665MHz";
		$data["Code"] = "40.665MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.675MHz";
		$data["Code"] = "40.675MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.685MHz";
		$data["Code"] = "40.685MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.695MHz";
		$data["Code"] = "40.695MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.715MHz";
		$data["Code"] = "40.715MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.725MHz";
		$data["Code"] = "40.725MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.735MHz";
		$data["Code"] = "40.735MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.765MHz";
		$data["Code"] = "40.765MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.775MHz";
		$data["Code"] = "40.775MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.785MHz";
		$data["Code"] = "40.785MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.815MHz";
		$data["Code"] = "40.815MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.825MHz";
		$data["Code"] = "40.825MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.835MHz";
		$data["Code"] = "40.835MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.865MHz";
		$data["Code"] = "40.865MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.875MHz";
		$data["Code"] = "40.875MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.885MHz";
		$data["Code"] = "40.885MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.915MHz";
		$data["Code"] = "40.915MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.925MHz";
		$data["Code"] = "40.925MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.935MHz";
		$data["Code"] = "40.935MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.965MHz";
		$data["Code"] = "40.965MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.975MHz";
		$data["Code"] = "40.975MHz";
		$wpdb->insert($tablename, $data);
		$data["Name"] = "40.985MHz";
		$data["Code"] = "40.985MHz";
		$wpdb->insert($tablename, $data);
	}	

	$tablename = $wpdb->prefix . TABLE_CLUBS;
	$sql = "CREATE TABLE IF " . $tablename . " (
			Id int unsigned not null auto_increment,
			Code varchar(20) not null,
			Name varchar(150) not null,
			UNIQUE KEY UN_Code (Code),
			PRIMARY KEY  (Id)
		);";
	create_table($tablename, $sql, "licensing_version", "0.5.3");

	if ($wpdb->get_var("SELECT COUNT(Code) FROM $tablename") == 0) {
		$data = array();

		$data["Code"] = "AMCA";
		$data["Name"] = "[AMCA] Auto Model Club Apeldoorn";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "DeSluis";
		$data["Name"] = "MBC De Sluis";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "ERCE";
		$data["Name"] = "[ERCE] Electro Racing Club Eindhoven";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "Euregio";
		$data["Name"] = "RC Club Euregio";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "HFCC";
		$data["Name"] = "[HFCC] Haagse Fiets Cross Club Hollandia";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "HMRC";
		$data["Name"] = "[HMRC] Helderse Model Race Club";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MRM";
		$data["Name"] = "[MRM] Model Racing Midland";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MAC de Baanbrekers";
		$data["Name"] = "Model Auto Club De BaanBrekers";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MACH";
		$data["Name"] = "[MACH] Model Auto Club Heemstede";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MACH-ONE";
		$data["Name"] = "[MACH-ONE] Model Auto Club Helmond";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MRCE";
		$data["Name"] = "[MRCE] Model Racing Club Elshout";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "MGR";
		$data["Name"] = "[MRG] Model Race Genk vzw";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "ORCD";
		$data["Name"] = "[ORCD] Off Road Club Drente";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "RACO";
		$data["Name"] = "[RACO] Raco 2000";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "RCCT";
		$data["Name"] = "[RCCT] RC Club Twente";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "RCHotWheels";
		$data["Name"] = "[HotWheels] Deventer Modelauto Club RC Hot Wheels";
		$wpdb->insert($tablename, $data);

		$data["Code"] = "REAL80";
		$data["Name"] = "[REAL80] Model Racing Club Real 80";
		$wpdb->insert($tablename, $data);
	}

	$tablename = $wpdb->prefix . TABLE_CLASS;
	$sql = "CREATE TABLE " . $tablename . " (
			Id int unsigned not null auto_increment,
			Code varchar(10) not null,
			Name varchar(150) not null,
			CloseDate DATE NULL,
			MaxDrivers tinyint NULL,
			MaxDriversCloseDate DATE NULL,
			Price tinyint NULL, 
			UNIQUE KEY UX_Code (Code),
			PRIMARY KEY  (Id)
		);";
	create_table($tablename, $sql, "licensing_version", "0.5.5");

	if ($wpdb->get_var("SELECT COUNT(Code) FROM $tablename") == 0) {
		$data = array( 
				"Code" => "BC10-NOMAC",
				"Name" => "Brandstof 1:10 200mm - NOMAC",
				"CloseDate" => "2012-06-01",
				"MaxDrivers" => NULL,
				"MaxDriversCloseDate" => NULL,
				"Price" => 90);
		$wpdb->insert($tablename, $data);
	
		$data["Code"] = "BC10-NK";
		$data["Name"] = "Brandstof 1:10 200mm - NK";
		$data["Price"] = 90;
		$wpdb->insert($tablename, $data);
	
		$data["Code"] = "EC10-STOCK";
		$data["Name"] = "Electro 1:10 - Stock";
		$data["Price"] = 100;
		$wpdb->insert($tablename, $data);
	
		$data["Code"] = "EC10-NK";
		$data["Name"] = "Electro 1:10 - Modified";
		$data["Price"] = 100;
		$wpdb->insert($tablename, $data);
	
		$data["Code"] = "EC10-PROTEN";
		$data["Name"] = "Electro 1:10 - Proten";
		$data["Price"] = 100;
		$wpdb->insert($tablename, $data);

		$data["Code"] = "EC10-LIC";
		$data["Name"] = "Electro 1:10 - Licentie";
		$data["Price"] = 40;
		$wpdb->insert($tablename, $data);

		$data["Code"] = "BC05-LIC";
		$data["Name"] = "Brandstof 1:5 - Licentie";
		$data["Price"] = 40;
		$wpdb->insert($tablename, $data);

		$data["Code"] = "BC05-NK";
		$data["Name"] = "Brandstof 1:5 - NK";
		$data["Price"] = 80;
		$wpdb->insert($tablename, $data);
		
		$data["Code"] = "OR06-NK";
		$data["Name"] = "OffRoad 1:6 - NK";
		$data["Price"] = 100;
		$wpdb->insert($tablename, $data);

		$data["Code"] = "OR06-LIC";
		$data["Name"] = "OffRoad 1:6 - Licentie";
		$data["Price"] = 40;
		$wpdb->insert($tablename, $data);

		$data["Code"] = "BC08";
		$data["MaxDrivers"] = 54;
		$data["MaxDriversCloseDate"] = "2012-03-18";
		$data["Name"] = "Brandstof 1:8";
		$data["Price"] = 100;
		$wpdb->insert($tablename, $data);
	
	}

	$wpdb->hide_errors();
	
}

?>
