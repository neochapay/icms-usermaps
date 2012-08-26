CREATE TABLE `#__places_checkin` (
`place_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `#__places_events` (
`object_type` tinytext NOT NULL,
`object_id` int(11) NOT NULL,
`x` double NOT NULL,
`y` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `#__actions` ( `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
('usermaps', 'add_checkin', 'Новая отметка', 'отметился в точке %s|', 1, 1);

