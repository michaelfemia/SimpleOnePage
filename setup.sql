CREATE TABLE `tblPasswordReset` (
  `fkMemberID` tinyint(4) NOT NULL,
  `fldVerificationCode` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblPageBlocks` (
  `pkPageBlockID` int(11) NOT NULL auto_increment,
  `fldPageName` varchar(35) NOT NULL,
  `fldPageLink` varchar(35) NOT NULL,
  `fldNavRank` smallint(2) NOT NULL,
  PRIMARY KEY  (`pkPageBlockID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;
CREATE TABLE `tblImages` (
  `pkImageID` int(11) NOT NULL auto_increment,
  `fldImageName` varchar(200) NOT NULL,
  PRIMARY KEY  (`pkImageID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;
CREATE TABLE `tblHomepageManagement` (
  `pkID` int(11) NOT NULL auto_increment,
  `fldHTMLID` varchar(80) NOT NULL,
  `fldContentType` tinyint(1) NOT NULL,
  `fldDescription` varchar(80) NOT NULL,
  `fldValue` varchar(1000) NOT NULL,
  PRIMARY KEY  (`pkID`),
  UNIQUE KEY `pkID` (`pkID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;
CREATE TABLE `tblHomePage` (
  `fldEntity` varchar(35) NOT NULL,
  `fldValue` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblGalleryBlocks` (
  `fkBlockElementID` int(15) NOT NULL,
  `fkImageID` int(15) NOT NULL,
  `fldRank` tinyint(3) NOT NULL,
  `fldCaption` varchar(300) default NULL,
  UNIQUE KEY `fkBlockElement` (`fkBlockElementID`,`fkImageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblContactTable` (
  `pkPersonID` int(11) NOT NULL auto_increment,
  `fldTitle` varchar(30) NOT NULL,
  `fldName` varchar(50) NOT NULL,
  `fldPhone` varchar(17) default NULL,
  `fldEmail` varchar(50) default NULL,
  PRIMARY KEY  (`pkPersonID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
CREATE TABLE `tblCMS` (
  `fldEntity` varchar(35) NOT NULL,
  `fldValue` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblBlockElementTypes` (
  `pkBlockElementTypeID` int(11) NOT NULL auto_increment,
  `fldBlockElementTypeName` varchar(100) NOT NULL,
  `fldBlockElementTypeOpeningTag` varchar(150) NOT NULL,
  `fldBlockElementTypeClosingTag` varchar(150) NOT NULL,
  `fldBlockElementTypeEditingNode` varchar(5000) NOT NULL,
  PRIMARY KEY  (`pkBlockElementTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;
CREATE TABLE `tblBlockElements` (
  `pkBlockElementID` int(11) NOT NULL auto_increment,
  `fkBlock` int(5) NOT NULL,
  `fkElementType` tinyint(2) NOT NULL,
  `fldPosition` tinyint(3) NOT NULL,
  `fldValue` varchar(5000) default 'Value',
  PRIMARY KEY  (`pkBlockElementID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

CREATE TABLE `members` (
  `id` int(11) NOT NULL auto_increment,
  `personname` varchar(35) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;
CREATE TABLE `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
