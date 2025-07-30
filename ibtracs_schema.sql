
-- IBTrACS Tropical Cyclone Database Schema
-- Generated for cyclone_db project

CREATE DATABASE IF NOT EXISTS `cyclone_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cyclone_db`;

-- Table: IBTrACS_Storms
CREATE TABLE IF NOT EXISTS `IBTrACS_Storms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sid` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100),
  `season_year` INT,
  `basin` VARCHAR(10),
  `track_time` DATETIME,
  `latitude` FLOAT,
  `longitude` FLOAT,
  `wind_wmo` INT,
  `pres_wmo` INT,
  `wind_us` INT,
  `pres_us` INT,
  `source` VARCHAR(50),
  `nature` VARCHAR(10),
  `sub_basin` VARCHAR(10),
  `iso_time` VARCHAR(30),
  `track_type` VARCHAR(20),
  `status` VARCHAR(20),
  `remarks` TEXT
);

-- Indexes
CREATE INDEX idx_sid ON `IBTrACS_Storms`(`sid`);
CREATE INDEX idx_name ON `IBTrACS_Storms`(`name`);
CREATE INDEX idx_year ON `IBTrACS_Storms`(`season_year`);
CREATE INDEX idx_basin ON `IBTrACS_Storms`(`basin`);
CREATE INDEX idx_wind ON `IBTrACS_Storms`(`wind_wmo`);
CREATE INDEX idx_pres ON `IBTrACS_Storms`(`pres_wmo`);
