-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Хост: custsqlmoo23
-- Время создания: Окт 19 2013 г., 00:49
-- Версия сервера: 5.1.56
-- Версия PHP: 4.4.9
-- 
-- БД: `autoimho`
-- 

-- --------------------------------------------------------

-- 
-- Структура таблицы `aim_users`
-- 

DROP TABLE IF EXISTS `aim_users`;
CREATE TABLE `aim_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(56) NOT NULL DEFAULT '',
  `login` varchar(40) NOT NULL DEFAULT '',
  `pass` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `isEditor` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  `isBlocked` tinyint(4) NOT NULL DEFAULT '0',
  `sendNews` tinyint(1) NOT NULL DEFAULT '1',
  `city` varchar(255) NOT NULL DEFAULT '',
  `filename` text NOT NULL,
  `birthDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sex` varchar(10) NOT NULL DEFAULT '',
  `regionID` varchar(5) NOT NULL DEFAULT '0',
  `create_dtm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `city_id` int(11) NOT NULL DEFAULT '0',
  `last_active` datetime NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `CountryID` int(11) NOT NULL,
  `old_rating` int(11) NOT NULL DEFAULT '0',
  `lastcommentviewID` int(11) DEFAULT '0',
  `lastIP` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`pass`),
  KEY `regionID` (`regionID`,`login`)
) ENGINE=MyISAM AUTO_INCREMENT=20452 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=20452 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `aimw_log`
-- 

DROP TABLE IF EXISTS `aimw_log`;
CREATE TABLE `aimw_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `obj_id` int(11) NOT NULL DEFAULT '0',
  `obj_name` varchar(50) NOT NULL DEFAULT '',
  `action_name` varchar(50) NOT NULL DEFAULT '',
  `create_dtm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`obj_id`),
  KEY `obj_id` (`obj_id`,`obj_name`,`action_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7337 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=7337 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_actual_det`
-- 

DROP TABLE IF EXISTS `chk_actual_det`;
CREATE TABLE `chk_actual_det` (
  `detid` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `detid` (`detid`),
  KEY `detid_2` (`detid`,`user_id`),
  KEY `product_id` (`product_id`,`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_barcode`
-- 

DROP TABLE IF EXISTS `chk_barcode`;
CREATE TABLE `chk_barcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=497 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=497 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_check_det`
-- 

DROP TABLE IF EXISTS `chk_check_det`;
CREATE TABLE `chk_check_det` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `head_id` int(11) NOT NULL DEFAULT '0',
  `cnt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `OnePrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isActual` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `isActual` (`isActual`),
  KEY `head_id` (`head_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10805 DEFAULT CHARSET=latin1 AUTO_INCREMENT=10805 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_check_head`
-- 

DROP TABLE IF EXISTS `chk_check_head`;
CREATE TABLE `chk_check_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0',
  `owner_id` int(11) NOT NULL DEFAULT '0',
  `create_dtm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DelMark` int(11) NOT NULL DEFAULT '0',
  `RealBuy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `create_dtm` (`create_dtm`),
  KEY `DelMark` (`DelMark`),
  KEY `owner_id` (`owner_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2362 DEFAULT CHARSET=latin1 AUTO_INCREMENT=2362 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_comment`
-- 

DROP TABLE IF EXISTS `chk_comment`;
CREATE TABLE `chk_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `create_dtm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`,`shop_id`,`product_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_friends`
-- 

DROP TABLE IF EXISTS `chk_friends`;
CREATE TABLE `chk_friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `friend_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(50) DEFAULT 'wait',
  `create_dtm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `user_id` (`user_id`,`friend_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=688 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=688 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_price_politics`
-- 

DROP TABLE IF EXISTS `chk_price_politics`;
CREATE TABLE `chk_price_politics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min_price` float NOT NULL DEFAULT '0',
  `max_price` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_product_barcode`
-- 

DROP TABLE IF EXISTS `chk_product_barcode`;
CREATE TABLE `chk_product_barcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=505 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=505 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_product_tag`
-- 

DROP TABLE IF EXISTS `chk_product_tag`;
CREATE TABLE `chk_product_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_products`
-- 

DROP TABLE IF EXISTS `chk_products`;
CREATE TABLE `chk_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `tags` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6737 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6737 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_report_product`
-- 

DROP TABLE IF EXISTS `chk_report_product`;
CREATE TABLE `chk_report_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `head_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_report_val_dic`
-- 

DROP TABLE IF EXISTS `chk_report_val_dic`;
CREATE TABLE `chk_report_val_dic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET cp1251 NOT NULL,
  `descript` text CHARACTER SET cp1251 NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_report_value`
-- 

DROP TABLE IF EXISTS `chk_report_value`;
CREATE TABLE `chk_report_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rep_val_type` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `dtm` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=333 DEFAULT CHARSET=latin1 AUTO_INCREMENT=333 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_reports`
-- 

DROP TABLE IF EXISTS `chk_reports`;
CREATE TABLE `chk_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL DEFAULT '0',
  `create_dtm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` tinytext NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_shop`
-- 

DROP TABLE IF EXISTS `chk_shop`;
CREATE TABLE `chk_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street_id` int(11) NOT NULL DEFAULT '0',
  `name` tinytext NOT NULL,
  `house_n` varchar(20) DEFAULT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `old_rating` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `street_id` (`street_id`)
) ENGINE=MyISAM AUTO_INCREMENT=773 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=773 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_user_plan`
-- 

DROP TABLE IF EXISTS `chk_user_plan`;
CREATE TABLE `chk_user_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cnt` decimal(10,2) NOT NULL,
  `product_name` varchar(250) CHARACTER SET cp1251 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=latin1 AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_v_actual_det`
-- 

DROP TABLE IF EXISTS `chk_v_actual_det`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`chk_v_actual_det` AS select max(`chk_v_det_by_user`.`id`) AS `detid`,`chk_v_det_by_user`.`product_id` AS `product_id`,`chk_v_det_by_user`.`shop_id` AS `shop_id`,`chk_v_det_by_user`.`user_id` AS `user_id` from `autoimho`.`chk_v_det_by_user` group by `chk_v_det_by_user`.`product_id`,`chk_v_det_by_user`.`shop_id`,`chk_v_det_by_user`.`user_id`;

-- --------------------------------------------------------

-- 
-- Структура таблицы `chk_v_det_by_user`
-- 

DROP TABLE IF EXISTS `chk_v_det_by_user`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`chk_v_det_by_user` AS select distinct `f`.`user_id` AS `user_id`,`d`.`id` AS `id`,`d`.`product_id` AS `product_id`,`h`.`shop_id` AS `shop_id` from (((`autoimho`.`chk_check_det` `d` join `autoimho`.`chk_check_head` `h` on((`h`.`id` = `d`.`head_id`))) left join `autoimho`.`chk_friends` `f` on(((`f`.`friend_id` = `h`.`owner_id`) or (`f`.`user_id` = `h`.`owner_id`)))) left join `autoimho`.`aim_users_params` `up` on(((`up`.`userID` = `h`.`owner_id`) and (`up`.`name` = _cp1251'openPrices')))) where ((`f`.`status` = _cp1251'friend') or (`up`.`paramValue` = 1));

-- --------------------------------------------------------

-- 
-- Структура таблицы `msg_inspect`
-- 

DROP TABLE IF EXISTS `msg_inspect`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`msg_inspect` AS select `m`.`dtm` AS `dtm`,`u1`.`login` AS `login`,`m`.`message` AS `message`,`u2`.`login` AS `login2` from ((`autoimho`.`aim_message` `m` join `autoimho`.`aim_users` `u1` on((`u1`.`id` = `m`.`user_id`))) join `autoimho`.`aim_users` `u2` on((`u2`.`id` = `m`.`user2_id`)));

-- --------------------------------------------------------

-- 
-- Структура таблицы `stat_commentfreq`
-- 

DROP TABLE IF EXISTS `stat_commentfreq`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`stat_commentfreq` AS select month(`autoimho`.`aim_comments`.`create_dtm`) AS `month( create_dtm )`,year(`autoimho`.`aim_comments`.`create_dtm`) AS `year( create_dtm )`,count(`autoimho`.`aim_comments`.`id`) AS `count( id )` from `autoimho`.`aim_comments` where (`autoimho`.`aim_comments`.`isActive` = 1) group by year(`autoimho`.`aim_comments`.`create_dtm`),month(`autoimho`.`aim_comments`.`create_dtm`) order by year(`autoimho`.`aim_comments`.`create_dtm`),month(`autoimho`.`aim_comments`.`create_dtm`);

-- --------------------------------------------------------

-- 
-- Структура таблицы `stat_postfreq`
-- 

DROP TABLE IF EXISTS `stat_postfreq`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`stat_postfreq` AS select month(`autoimho`.`aim_rates`.`create_dtm`) AS `month( create_dtm )`,year(`autoimho`.`aim_rates`.`create_dtm`) AS `year( create_dtm )`,count(`autoimho`.`aim_rates`.`id`) AS `count( id )` from `autoimho`.`aim_rates` where (`autoimho`.`aim_rates`.`isActive` = 1) group by year(`autoimho`.`aim_rates`.`create_dtm`),month(`autoimho`.`aim_rates`.`create_dtm`) order by year(`autoimho`.`aim_rates`.`create_dtm`),month(`autoimho`.`aim_rates`.`create_dtm`);

-- --------------------------------------------------------

-- 
-- Структура таблицы `stat_regfreq`
-- 

DROP TABLE IF EXISTS `stat_regfreq`;
CREATE ALGORITHM=UNDEFINED DEFINER=`skyscream`@`10.%` SQL SECURITY DEFINER VIEW `autoimho`.`stat_regfreq` AS select month(`autoimho`.`aim_users`.`create_dtm`) AS `month(create_dtm)`,year(`autoimho`.`aim_users`.`create_dtm`) AS `year(create_dtm)`,count(`autoimho`.`aim_users`.`id`) AS `count(id)` from `autoimho`.`aim_users` where ((`autoimho`.`aim_users`.`isActive` = 1) and (`autoimho`.`aim_users`.`create_dtm` > _utf8'2010-01-01')) group by year(`autoimho`.`aim_users`.`create_dtm`),month(`autoimho`.`aim_users`.`create_dtm`) order by year(`autoimho`.`aim_users`.`create_dtm`),month(`autoimho`.`aim_users`.`create_dtm`);
