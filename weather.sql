-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for weather
DROP DATABASE IF EXISTS `weather`;
CREATE DATABASE IF NOT EXISTS `weather` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `weather`;

-- Dumping structure for table weather.agency_storm_intensity
DROP TABLE IF EXISTS `agency_storm_intensity`;
CREATE TABLE IF NOT EXISTS `agency_storm_intensity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ibtracs_id` varchar(100) DEFAULT NULL,
  `agency` varchar(50) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `wind_kts` int DEFAULT NULL,
  `pressure_mb` int DEFAULT NULL,
  `classification` varchar(50) DEFAULT NULL,
  `source_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_agency_sid` (`ibtracs_id`),
  KEY `idx_agency_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.agency_storm_intensity: ~0 rows (approximately)
DELETE FROM `agency_storm_intensity`;

-- Dumping structure for table weather.basins
DROP TABLE IF EXISTS `basins`;
CREATE TABLE IF NOT EXISTS `basins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `ocean` varchar(50) DEFAULT NULL,
  `hemisphere` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.basins: ~0 rows (approximately)
DELETE FROM `basins`;

-- Dumping structure for table weather.basin_metadata
DROP TABLE IF EXISTS `basin_metadata`;
CREATE TABLE IF NOT EXISTS `basin_metadata` (
  `basin_code` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `hemisphere` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`basin_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.basin_metadata: ~0 rows (approximately)
DELETE FROM `basin_metadata`;

-- Dumping structure for table weather.hurdat_storms
DROP TABLE IF EXISTS `hurdat_storms`;
CREATE TABLE IF NOT EXISTS `hurdat_storms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `storm_name` varchar(100) DEFAULT NULL,
  `year` int DEFAULT NULL,
  `basin` varchar(50) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `wind_kts` int DEFAULT NULL,
  `pressure_mb` int DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.hurdat_storms: ~0 rows (approximately)
DELETE FROM `hurdat_storms`;

-- Dumping structure for table weather.ibtracs_storms
DROP TABLE IF EXISTS `ibtracs_storms`;
CREATE TABLE IF NOT EXISTS `ibtracs_storms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `season` varchar(10) DEFAULT NULL,
  `basin` varchar(10) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `wind_wmo` int DEFAULT NULL,
  `pressure_wmo` int DEFAULT NULL,
  `track_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.ibtracs_storms: ~0 rows (approximately)
DELETE FROM `ibtracs_storms`;

-- Dumping structure for table weather.ibtracs_storms_obsolete
DROP TABLE IF EXISTS `ibtracs_storms_obsolete`;
CREATE TABLE IF NOT EXISTS `ibtracs_storms_obsolete` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `basin` varchar(50) DEFAULT NULL,
  `agency` varchar(50) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `wind_kts` int DEFAULT NULL,
  `pressure_mb` int DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `storm_type` varchar(50) DEFAULT NULL,
  `nature` varchar(50) DEFAULT NULL,
  `track_type` varchar(50) DEFAULT NULL,
  `track_points` int DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `landfall_count` int DEFAULT NULL,
  `max_wind_kts` int DEFAULT NULL,
  `min_pressure_mb` int DEFAULT NULL,
  `comments` text,
  `storm_num` int DEFAULT NULL,
  `season_year` int DEFAULT NULL,
  `storm_classification` varchar(50) DEFAULT NULL,
  `source_notes` text,
  `season` year DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sid` (`sid`),
  KEY `idx_ibtracs_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.ibtracs_storms_obsolete: ~0 rows (approximately)
DELETE FROM `ibtracs_storms_obsolete`;

-- Dumping structure for table weather.ibtracs_storms_obsolete2
DROP TABLE IF EXISTS `ibtracs_storms_obsolete2`;
CREATE TABLE IF NOT EXISTS `ibtracs_storms_obsolete2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `season_year` int DEFAULT NULL,
  `basin` varchar(10) DEFAULT NULL,
  `track_time` datetime DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `wind_wmo` int DEFAULT NULL,
  `pres_wmo` int DEFAULT NULL,
  `wind_us` int DEFAULT NULL,
  `pres_us` int DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `nature` varchar(10) DEFAULT NULL,
  `sub_basin` varchar(10) DEFAULT NULL,
  `iso_time` varchar(30) DEFAULT NULL,
  `track_type` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`id`),
  KEY `idx_sid` (`sid`),
  KEY `idx_name` (`name`),
  KEY `idx_year` (`season_year`),
  KEY `idx_basin` (`basin`),
  KEY `idx_wind` (`wind_wmo`),
  KEY `idx_pres` (`pres_wmo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.ibtracs_storms_obsolete2: ~0 rows (approximately)
DELETE FROM `ibtracs_storms_obsolete2`;

-- Dumping structure for table weather.ibtracs_tracks
DROP TABLE IF EXISTS `ibtracs_tracks`;
CREATE TABLE IF NOT EXISTS `ibtracs_tracks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` varchar(100) DEFAULT NULL,
  `point_order` int DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `wind_kts` int DEFAULT NULL,
  `pressure_mb` int DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `storm_type` varchar(50) DEFAULT NULL,
  `nature` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  CONSTRAINT `ibtracs_tracks_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `ibtracs_storms_obsolete` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.ibtracs_tracks: ~0 rows (approximately)
DELETE FROM `ibtracs_tracks`;

-- Dumping structure for table weather.proxy_sites
DROP TABLE IF EXISTS `proxy_sites`;
CREATE TABLE IF NOT EXISTS `proxy_sites` (
  `site_id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `proxy_type` varchar(100) DEFAULT NULL,
  `record_start_year` int DEFAULT NULL,
  `record_end_year` int DEFAULT NULL,
  `resolution` varchar(50) DEFAULT NULL,
  `source` text,
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.proxy_sites: ~0 rows (approximately)
DELETE FROM `proxy_sites`;

-- Dumping structure for table weather.proxy_storms
DROP TABLE IF EXISTS `proxy_storms`;
CREATE TABLE IF NOT EXISTS `proxy_storms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `proxy_type` varchar(50) DEFAULT NULL,
  `start_year` int DEFAULT NULL,
  `end_year` int DEFAULT NULL,
  `storm_date_range` varchar(50) DEFAULT NULL,
  `estimated_intensity` varchar(50) DEFAULT NULL,
  `notes` text,
  `source_reference` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.proxy_storms: ~0 rows (approximately)
DELETE FROM `proxy_storms`;

-- Dumping structure for table weather.storm_lifecycle
DROP TABLE IF EXISTS `storm_lifecycle`;
CREATE TABLE IF NOT EXISTS `storm_lifecycle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` varchar(100) DEFAULT NULL,
  `stage` varchar(50) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  CONSTRAINT `storm_lifecycle_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `ibtracs_storms_obsolete` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.storm_lifecycle: ~0 rows (approximately)
DELETE FROM `storm_lifecycle`;

-- Dumping structure for table weather.tcdatabase
DROP TABLE IF EXISTS `tcdatabase`;
CREATE TABLE IF NOT EXISTS `tcdatabase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(522) NOT NULL DEFAULT '0',
  `storm_id` varchar(522) DEFAULT NULL,
  `name` varchar(522) DEFAULT NULL,
  `basin` varchar(522) DEFAULT NULL,
  `wind_speed` int DEFAULT NULL,
  `pressure` int DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `fatalities` varchar(522) DEFAULT NULL,
  `damages` decimal(10,2) DEFAULT NULL,
  `ace` decimal(10,4) DEFAULT NULL,
  `satellite_image` varchar(255) DEFAULT NULL,
  `history` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.tcdatabase: ~1 rows (approximately)
DELETE FROM `tcdatabase`;
INSERT INTO `tcdatabase` (`id`, `image`, `storm_id`, `name`, `basin`, `wind_speed`, `pressure`, `start_date`, `end_date`, `fatalities`, `damages`, `ace`, `satellite_image`, `history`) VALUES
	(2, 'uploads/1754889292_One_1851_path.png', 'AL011851', 'ONE', 'NATL', 90, 977, '1851-06-25 00:00:00', '1851-06-28 00:00:00', '1(2)', 0.00, 0.0000, 'uploads/1754889292_Temporary_cyclone_north.png', '');

-- Dumping structure for table weather.tornado_db
DROP TABLE IF EXISTS `tornado_db`;
CREATE TABLE IF NOT EXISTS `tornado_db` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(522) NOT NULL DEFAULT '0',
  `tor_location` varchar(522) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `fujita_rank` enum('EFU','EF0','EF1','EF2','EF3','EF4','EF5') DEFAULT NULL,
  `wind_speed` varchar(522) DEFAULT NULL,
  `max_width` decimal(10,2) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `duration` varchar(522) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.tornado_db: ~0 rows (approximately)
DELETE FROM `tornado_db`;

-- Dumping structure for table weather.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(522) DEFAULT NULL,
  `email` varchar(522) DEFAULT NULL,
  `password` varchar(522) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table weather.users: ~1 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `remember_token`) VALUES
	(1, 'Steven Weathers', 'stevenweathersyt@gmail.com', '$2y$10$Xs/vm0cdKTppRmRVDVYZauBozvUJu9SDQJEmargc9cjLxNU8ydux.', 'bfeb6e98725ef7a7b1a123fa7d71b8d9de1801e885727874b4695fcbe74438bd');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
