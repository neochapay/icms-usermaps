CREATE TABLE IF NOT EXISTS `cms_places_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET cp1251 NOT NULL,
  `title` varchar(128) CHARACTER SET cp1251 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `cms_places_category` (`id`, `name`, `title`) VALUES
(1, 'user', 'Пользовтатель');

UPDATE cms_places SET `type` = 1 

ALTER TABLE `cms_places` CHANGE `type` `type_id` INT( 11 ) NOT NULL 
ALTER TABLE  `cms_places` CHANGE  `data`  `title` VARCHAR( 128 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL
ALTER TABLE  `cms_places` ADD  `body` VARCHAR( 128 ) NOT NULL
