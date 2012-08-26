DROP TABLE IF EXISTS `#__places`;

CREATE TABLE `#__places` (
`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type_id` INT(11) NOT NULL ,
`user_id` INT(11) NOT NULL ,
`x` DOUBLE NOT NULL,
`y` DOUBLE NOT NULL,
`title` VARCHAR(128) NOT NULL ,
`body` LONGTEXT NOT NULL ,
KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `#__places_category`;

CREATE TABLE `#__places_category` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(128) NOT NULL,
`title` varchar(128) NOT NULL,
`is_root` int(11) NOT NULL,
`root_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

INSERT INTO `#__places_category` (`id`, `name`, `title`, `is_root`, `root_id`) VALUES (1, 'user', 'Пользователь', 0, 0);



DROP TABLE IF EXISTS `#__places_checkin`;

CREATE TABLE IF NOT EXISTS `#__places_checkin` (
`place_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `#__places_events`;

CREATE TABLE IF NOT EXISTS `#__places_events` (
`object_type` tinytext NOT NULL,
`object_id` int(11) NOT NULL,
`x` double NOT NULL,
`y` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;



INSERT INTO `#__actions` (`component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES ('usermaps', 'add_place', 'Добавление места', 'добавляет место %s', 1, 1);



INSERT INTO `#__actions` ( `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
('usermaps', 'add_checkin', 'Новая отметка', 'отметился в точке %s|', 1, 1);



INSERT INTO `#__modules` (`position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `access_list`, `cache`, `cachetime`, `cacheint`, `template`, `is_strict_bind`, `version`) VALUES
('maintop', 'Карта пользователей', 'Карта пользователей', 1, 'mod_usermaps_mapview', 1, 1, 1, 0, '---\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '0.5');



INSERT INTO `#__plugins` (`plugin`, `title`, `description`, `author`, `version`, `plugin_type`, `published`, `config`) VALUES
('p_usermaps_sosedi', 'Рядом на карте', 'Добавляет вкладку "Рядом" в профили всех пользователей', 'Сергей Игоревич (NeoChapay)', '0.1', 'plugin', 1, '---\nКоличество объектов: 10\nКвадрат поиска в метрах: 500\n'),
('p_usermaps_photo', 'Фото на карте', 'Позволяет привязать фотографию к точке на карте', 'NeoChapay', '0.6', 'plugin', 1, '---\n');

INSERT INTO `#__comment_targets` (`target`, `component`, `title`) VALUES ('point', 'usermaps', 'Пользовательские карты');
