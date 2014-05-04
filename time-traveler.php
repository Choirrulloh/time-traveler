#!/usr/bin/php -q
<?php
/* Copyright (c) 2014 Viraj Chitnis. All rights reserved.
 * This program is available under the MIT License.
 *
 * Time Traveler is a system backup program that should work on any
 * Unix based system. The configuration file for this program is
 * located at /etc/timetraveler/config.json.
 * The config file is formatted as a human readable json file, for
 * this program to function properly, please leave the formatting
 * of the config file intact after editing it.
 *
 * Dependencies: rsync & php
 */

// ****************************************************************

/* User servicable variables
 */

// Location for storing the backups, this can either be a mounted
// network location or a locally attached external storage device.
$backup_dir = "/mnt/Backups";

// System timezone
$sys_timezone = "America/New_York";

// Backup tags filename
$json_tags = "backup_tags.json";

// MySQL root password
$mysql_root_password = "nfscarbon";

// List of excludes
$excludes = array(
	"/var/www/movies.virajchitnis.com/converted/*",
	"/var/cache/squid/*"
);

// Keep monthly backups for x months
$monthly_backups = 6;

// Keep weekly backups for x weeks
$weekly_backups = 4;

// Keep daily backups for x days
$daily_backups = 7;

// Keep hourly backups for x hours
$hourly_backups = 24;

// ****************************************************************

// Non user servicable variables
// Do not change these variables unless you know what you are doing.
$latest_backup;
$curr_epoch_time = time();
// Directories to always be excluded
$system_excludes = array(
	"/lost+found",
	"/dev/*",
	"/proc/*",
	"/sys/*",
	"/tmp/*",
	"/run/*",
	"/mnt/*",
	"/media/*"
);

// Set default timezone for script
date_default_timezone_set($sys_timezone);

// Go to backup location
chdir($backup_dir);

// Purge older backups
$all_backups = scandir("./");

foreach ($all_backups as &$curr_backup) {
	// Check if the directory is a backup
	if (!(is_dir($curr_backup) && file_exists($curr_backup."/root/timestamp.json"))) {
		continue;
	}
	
	// Load data from json
	$curr_backup_data = json_decode(file_get_contents($curr_backup."/root/".$json_tags), true);
	// Backup age
	$backup_time = $curr_backup_data['epoch_time'];
	// Backup max oldness
	$backup_oldness = $curr_backup_data['oldness'];
	// Latest backup or not
	$backup_latest = $curr_backup_data['latest'];
	
	// If its a monthly backup and its older than 6 months, delete it
	if ((($curr_epoch_time - $backup_time) >= (3600 * 24 * 30 * $monthly_backups)) && ($backup_oldness['monthly'] == true)) {
		exec("rm -r ".escapeshellarg($curr_backup));
	}
	
	// If its a weekly backup and its older than one month, delete it
	if ((($curr_epoch_time - $backup_time) >= (3600 * 24 * 7 * $weekly_backups)) && (($backup_oldness['weekly'] == true) && ($backup_oldness['monthly'] == false))) {
		exec("rm -r ".escapeshellarg($curr_backup));
	}
	
	// If its a daily backup and its older than one week, delete it
	if ((($curr_epoch_time - $backup_time) >= (3600 * 24 * $daily_backups)) && (($backup_oldness['daily'] == true) && ($backup_oldness['weekly'] == false) && ($backup_oldness['monthly'] == false))) {
		exec("rm -r ".escapeshellarg($curr_backup));
	}
	
	// If its a hourly backup and its older than 24hrs, delete it
	if ((($curr_epoch_time - $backup_time) >= (3600 * $hourly_backups)) && (($backup_oldness['hourly'] == true) && ($backup_oldness['daily'] == false) && ($backup_oldness['weekly'] == false) && ($backup_oldness['monthly'] == false))) {
		exec("rm -r ".escapeshellarg($curr_backup));
	}
	
	// Get latest backup
	if ($backup_latest == true) {
		$latest_backup = $curr_backup;
		
		// Unmark latest backup
		$curr_backup_data['latest'] = false;
		file_put_contents($curr_backup."/root/".$json_tags, json_encode($curr_backup_data));
	}
}

// Check the date, insert a hourly tag, if its 3am, insert a daily tag,
// if its 3am on sunday, insert a weekly tag, if its 3am on the first of
// the month, insert a monthly tag
$curr_hour = date('H');
$curr_day_of_week = date('N');
$curr_date = date('d');

// Create array of backup tags
$tags = array(
	"epoch_time" => time(),
	"latest" => true,
	"oldness" => array(
		"monthly" => ($curr_date == "01") ? true : false,
		"weekly" => ($curr_day_of_week == "7") ? true : false,
		"daily" => ($curr_hour == "03") ? true : false,
		"hourly" => true
	)
);

// Store tags in json file
file_put_contents("/root/".$json_tags, json_encode($tags));

// Backup all databases
exec("mysqldump -u root -p".escapeshellarg($mysql_root_password)." --events --all-databases > /root/mysql_backup.sql");

// Backup the current system root
echo shell_exec("rsync -aAv --delete --link-dest=".escapeshellarg($backup_dir)."/".escapeshellarg($latest_backup)." / ".escapeshellarg($backup_dir)."/".escapeshellarg(date('Y-m-d-H-i-s')).".backup --exclude={".implode(",", $system_excludes).",".implode(",", $excludes)."}");

// Final cleanup
exec("rm /root/mysql_backup.sql");
exec("/root/".escapeshellarg($json_tags));

// Backup complete

?>