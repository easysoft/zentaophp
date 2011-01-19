CREATE TABLE IF NOT EXISTS `blog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(120) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
