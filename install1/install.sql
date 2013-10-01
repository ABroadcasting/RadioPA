SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `dj` (
  `id` tinyint(50) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `dj` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `admin` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dj` (`dj`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

CREATE TABLE IF NOT EXISTS `last_zakaz` (
  `id` varchar(15) NOT NULL,
  `idsong` varchar(15) NOT NULL,
  `track` varchar(100) NOT NULL,
  `time` varchar(25) NOT NULL,
  `skolko` varchar(10) NOT NULL,
  `ip` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `login` (
  `ip` varchar(25) NOT NULL,
  `dj` varchar(50) NOT NULL,
  `raz` tinyint(10) NOT NULL,
  `time` varchar(25) NOT NULL,
  `hash` varchar(25) NOT NULL,
  `admin` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `playmode` tinyint(4) DEFAULT NULL,
  `enable` tinyint(4) DEFAULT NULL,
  `event1` text,
  `event2` text,
  `now` tinyint(4) DEFAULT NULL,
  `show` tinyint(4) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `last_time` bigint(20) DEFAULT NULL,
  `allow_zakaz` int(11) DEFAULT '1',
  `auto` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `poisk` (
  `title` varchar(50) NOT NULL,
  `artist` varchar(50) NOT NULL,
  `id` int(10) NOT NULL,
  `idsong` int(11) NOT NULL,
  `filename` text NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(25) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `songlist` (
  `idsong` int(11) NOT NULL AUTO_INCREMENT,
  `zakazano` int(10) NOT NULL,
  `id` int(11) DEFAULT NULL,
  `filename` text,
  `artist` text,
  `title` text,
  `album` text,
  `genre` text,
  `albumyear` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `played` int(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`idsong`),
  FULLTEXT KEY `artist` (`artist`,`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `statistic` (
  `type` varchar(50) NOT NULL,
  `country` varchar(20) NOT NULL,
  `country_name` varchar(25) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `client` varchar(150) NOT NULL,
  `listeners` varchar(15) NOT NULL,
  `time` int(20) NOT NULL,
  `date` varchar(10) NOT NULL,
  KEY `stream` (`listeners`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tracklist` (
  `title` text,
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `idsong` int(11) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `time` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `user_ip` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL,
  `time` varchar(100) NOT NULL,
  `nomer` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `zakaz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idsong` int(10) NOT NULL,
  `filename` text,
  `artist` text,
  `title` text,
  `album` text,
  `duration` int(11) DEFAULT NULL,
  `admin` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
