SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `desafio_alpha_alphacore` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `desafio_alpha_alphacore` ;

-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`event`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`event` (
  `event_id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NULL ,
  `start_date` TIMESTAMP NULL ,
  `end_date` TIMESTAMP NULL ,
  `description` VARCHAR(400) NULL ,
  PRIMARY KEY (`event_id`) ,
  INDEX `start_date` (`start_date` ASC) ,
  UNIQUE INDEX `event_id_UNIQUE` (`event_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Datas de eventos usados na aplicação';


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`role` (
  `role_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`role_id`) ,
  UNIQUE INDEX `profile_id_UNIQUE` (`role_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`user` (
  `user_id` VARCHAR(40) NOT NULL ,
  `username` VARCHAR(255) NULL ,
  `password` VARCHAR(255) NULL ,
  `password_salt` VARCHAR(40) NULL ,
  `role_id` INT NULL DEFAULT 1 ,
  `last_login` TIMESTAMP NULL ,
  PRIMARY KEY (`user_id`) ,
  INDEX `fk_profile_id` (`role_id` ASC) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC) ,
  CONSTRAINT `fk_profile_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `desafio_alpha_alphacore`.`role` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = 'Credenciais usadas na autenticação da aplicação';


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`token`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`token` (
  `token_id` INT NOT NULL AUTO_INCREMENT ,
  `creation_time` TIMESTAMP NULL DEFAULT NULL ,
  `user_id` VARCHAR(40) NULL DEFAULT NULL ,
  `token` VARCHAR(40) NULL DEFAULT NULL ,
  `token_salt` VARCHAR(40) NULL DEFAULT NULL ,
  PRIMARY KEY (`token_id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `desafio_alpha_alphacore`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`team`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`team` (
  `team_id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` VARCHAR(40) NULL ,
  `name` VARCHAR(255) NULL ,
  `email` VARCHAR(255) NULL ,
  PRIMARY KEY (`team_id`) ,
  INDEX `fk_team_userid` (`user_id` ASC) ,
  CONSTRAINT `fk_team_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `desafio_alpha_alphacore`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`country`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`country` (
  `country_id` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  `abbr` VARCHAR(10) NULL ,
  PRIMARY KEY (`country_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`state`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`state` (
  `state_id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `abbr` VARCHAR(45) NULL ,
  `country_id` INT NULL ,
  PRIMARY KEY (`state_id`) ,
  INDEX `fk_country_id` (`country_id` ASC) ,
  CONSTRAINT `fk_country_id`
    FOREIGN KEY (`country_id` )
    REFERENCES `desafio_alpha_alphacore`.`country` (`country_id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`city`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`city` (
  `city_id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `state_id` INT NULL ,
  PRIMARY KEY (`city_id`) ,
  INDEX `fk_state_id` (`state_id` ASC) ,
  CONSTRAINT `fk_state_id`
    FOREIGN KEY (`state_id` )
    REFERENCES `desafio_alpha_alphacore`.`state` (`state_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`person`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`person` (
  `person_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `team_id` INT(11) NULL DEFAULT NULL ,
  `user_id` VARCHAR(40) NULL DEFAULT NULL ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `email` VARCHAR(45) NULL DEFAULT NULL ,
  `birthday` VARCHAR(45) NULL DEFAULT NULL ,
  `address` VARCHAR(45) NULL DEFAULT NULL ,
  `city_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`person_id`) ,
  INDEX `fk_team_id` (`team_id` ASC) ,
  INDEX `fk_person_city_id` (`city_id` ASC) ,
  INDEX `fk_person_userid` (`user_id` ASC) ,
  CONSTRAINT `fk_person_city_id`
    FOREIGN KEY (`city_id` )
    REFERENCES `desafio_alpha_alphacore`.`city` (`city_id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_person_userid`
    FOREIGN KEY (`user_id` )
    REFERENCES `desafio_alpha_alphacore`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `desafio_alpha_alphacore`.`resource`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `desafio_alpha_alphacore`.`resource` (
  `resource_id` INT NOT NULL AUTO_INCREMENT ,
  `module` VARCHAR(45) NULL ,
  `controller` VARCHAR(45) NULL ,
  `action` VARCHAR(45) NULL ,
  PRIMARY KEY (`resource_id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
