CREATE TABLE IF NOT EXISTS `siteViews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteName` varchar(40) NOT NULL,
	`rating` int(5) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `age` int(5) NOT NULL,
  `gender` int(5) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(30) NOT NULL,
  `country` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2250 ;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`viewId` int(11) NOT NULL,
  `siteName` varchar(40) NOT NULL,
	`rating` int(5) NOT NULL,
  `tag` varchar(30) NOT NULL,
  `count` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2250 ;
