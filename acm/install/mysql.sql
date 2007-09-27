CREATE TABLE `$__acm_config` (
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `$__acm_config` (`name`, `value`) VALUES 
('acc_max_number', '999999'),
('acc_min_number', '1000'),
('admin_email', ''),
('admin_login', 'admin'),
('admin_password', 'pass'),
('antiflood_ip', '1'),
('antiflood_ip_time', '900'),
('base_url', 'http://localhost/acm'),
('default_group', '1'),
('depots_chest', '2594'),
('depots_item', '2590'),
('lang', 'English'),
('map_name', 'map.otbm'),
('ots_depots', '3'),
('ots_dir', './otserv/data'),
('pass_min_length', '6'),
('rook', '0'),
('rook_town', '1'),
('mail_via_smtp', '1'),
('smtp_host', ''),
('smtp_pass', ''),
('smtp_user', ''),
('style', 'default'),
('timeout_online', '180'),
('title', 'ACM 3.0 Alpha'),
('use_gz', '0'),
('max_acc_chars', '5'),
('name_min_length', '3'),
('name_max_length', '18'),
('use_md5', '1');

CREATE TABLE `$__acm_containers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `content` int(11) default NULL,
  `slot` int(11) default NULL,
  `count` int(11) default NULL,
  `profile` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `profile` (`profile`),
  KEY `slot` (`slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1001 ;


CREATE TABLE `$__acm_profiles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `skill0` int(11) default NULL,
  `skill1` int(11) default NULL,
  `skill2` int(11) default NULL,
  `skill3` int(11) default NULL,
  `skill4` int(11) default NULL,
  `skill5` int(11) default NULL,
  `skill6` int(11) default NULL,
  `health` int(11) default NULL,
  `healthmax` int(11) default NULL,
  `direction` int(11) default NULL,
  `experience` int(11) default NULL,
  `lookbody` int(11) default NULL,
  `lookfeet` int(11) default NULL,
  `lookhead` int(11) default NULL,
  `looklegs` int(11) default NULL,
  `looktype` int(11) default NULL,
  `maglevel` int(11) default NULL,
  `mana` int(11) default NULL,
  `manamax` int(11) default NULL,
  `manaspent` int(11) default NULL,
  `soul` int(11) default NULL,
  `cap` int(11) default NULL,
  `food` int(11) default NULL,
  `loss_experience` int(11) default NULL,
  `loss_mana` int(11) default NULL,
  `loss_skills` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `$__acm_profiles` (`id`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food`, `loss_experience`, `loss_mana`, `loss_skills`) VALUES
(1, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(2, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(3, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(4, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(5, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(11, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(12, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(13, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(14, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10),
(15, 10, 10, 10, 10, 10, 10, 10, 250, 250, 3, 1, 30, 50, 20, 40, 128, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);


CREATE TABLE IF NOT EXISTS `acm_antiflood` (
  `ip` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8_unicode_ci;