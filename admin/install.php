<?php
defined('ABSPATH') or die('No script kiddies please!');

function installPlugin()
{
    global $wpdb;
    // quiz table schema
    $jsQuizTable = $wpdb->prefix . 'jsquiz_tbl';
    if (!$wpdb->get_var("SHOW TABLES LIKE '$jsQuizTable'")) {
        $sqlQuizTable = "CREATE TABLE IF NOT EXISTS `$jsQuizTable` (
                              `qid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `title` varchar(255) NOT NULL,
                              `slug` varchar(255) NOT NULL,
                              `status` tinyint(1) NOT NULL DEFAULT '1',
                              `gameTime` varchar(40) NOT NULL DEFAULT '6',
                              `startDate` datetime DEFAULT '0000-00-00 00:00:00',
                              `endDate` datetime DEFAULT '0000-00-00 00:00:00',
                              `dateCreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $wpdb->query($sqlQuizTable);
    }

    // answers table schema
    $jsQuizAnswersTable = $wpdb->prefix . 'jsquiz_answers_tbl';
    if (!$wpdb->get_var("SHOW TABLES LIKE '$jsQuizAnswersTable'")) {
        $sqlQuizAnswersTable = "CREATE TABLE IF NOT EXISTS `$jsQuizAnswersTable` (
                                    `aid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `qid` int(11),
                                    `answer` varchar(255) NOT NULL,
                                    `image` varchar(255) NOT NULL,
                                    `dateCreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $wpdb->query($sqlQuizAnswersTable);
    }

    // scores table schema
    $jsQuizScoresTable = $wpdb->prefix . 'jsquiz_scores_tbl';
    if (!$wpdb->get_var("SHOW TABLES LIKE '$jsQuizScoresTable'")) {
        $sqlQuizScoresTable = "CREATE TABLE IF NOT EXISTS `$jsQuizScoresTable` (
                                    `sid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `qid` int(11),
                                    `title` varchar(255) NOT NULL DEFAULT '',
                                    `score` int(11) NOT NULL DEFAULT 0,
                                    `percentScore` int(11) NOT NULL DEFAULT 0,
                                    `timeToComplete` varchar(40) NOT NULL DEFAULT '0',
                                    `isGiveUp` int(1) NOT NULL DEFAULT 0,
                                    `winnerId` varchar(255) NOT NULL DEFAULT '0',
                                    `ip` varchar(255),
                                    `winDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $wpdb->query($sqlQuizScoresTable);
    }

}

function uninstallPlugin()
{
    global $wpdb;

    // first remove all tables
    $jsQuizTables =  $wpdb->prefix . 'jsquiz_tbl' . ', ' . 
                    $wpdb->prefix . 'jsquiz_answers_tbl' . ', ' . 
                    $wpdb->prefix . 'jsquiz_scores_tbl';
    // $wpdb->query("DROP TABLE IF EXISTS $jsQuizTables");
}
