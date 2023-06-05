CREATE SCHEMA `[[yourDatabaseName]]` ;

CREATE TABLE `[[yourDatabaseName]]`.`preusers` (
  `token` VARCHAR(255) NOT NULL,
  `mail` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`token`),
  UNIQUE INDEX `token_UNIQUE` (`token` ASC) VISIBLE,
  UNIQUE INDEX `mail_UNIQUE` (`mail` ASC) VISIBLE);

CREATE TABLE `[[yourDatabaseName]]`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mail` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `mail_UNIQUE` (`mail` ASC) VISIBLE);