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

CREATE TABLE `tblBlockElementTypes` (
  `pkTypeID` int(11) NOT NULL auto_increment,
  `fldTypeName` varchar(100) NOT NULL,
  `fldSelectName` varchar(80) NOT NULL,
  `fldContentType` tinyint(4) NOT NULL,
  `fldHTMLType` varchar(10) NOT NULL,
  PRIMARY KEY  (`pkTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tblBlockElementTypes`
--

INSERT INTO `tblBlockElementTypes` VALUES(1, 'blockHeadline', 'Headline', 1, 'h2');
INSERT INTO `tblBlockElementTypes` VALUES(2, 'blockText', 'Paragraph Text', 2, 'p');
INSERT INTO `tblBlockElementTypes` VALUES(3, 'galleryStrip', 'Filmstrip Gallery', 4, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(4, 'table', '3- Column Table', 5, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(5, 'largeImage', 'Full Screen Image', 3, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(6, 'galleryLinks', 'Modal Gallery', 4, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(7, 'parallaxParent', 'Full Parallax', 3, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(8, 'imageArticleLink', 'Image Links', 4, 'div');
INSERT INTO `tblBlockElementTypes` VALUES(9, 'quote', 'Quote', 2, 'p');
INSERT INTO `tblBlockElementTypes` VALUES(10, 'vimeoEmbed', 'Vimeo Embed', 6, 'iframe');

INSERT INTO `tblPageBlocks` (`pkPageBlockID`, `fldPageName`, `fldPageLink`, `fldNavRank`) VALUES
(40, 'New Section', 'newsection', 1);

INSERT INTO `tblBlockElements` (`pkBlockElementID`, `fkBlock`, `fkElementType`, `fldPosition`, `fldValue`) VALUES
(90, 40, 1, 1, 'This is a page section.'),
(91, 40, 2, 2, 'Clicking MENU in the top left corner opens a set of controls to add and remove sections, edit their names, and rearrange their order. If your sections get lengthy, you can toggle them closed by clicking the section name that precedes each section editor, in bold black font.'),
(97, 40, 2, 3, 'To add child elements to a section, use the controls at the bottom of the editor (a dropdown menu with an "Add" button next to it). There are a number of text and image formatting options. Rearrange or delete these child elements with the controls to the leftâ€“ the up/down arrows and trash.'),
(99, 40, 2, 4, 'To edit headers, paragraphs, quotes, and tables, simply click the text you want to edit, and an input field will appear. Press enter/return or click anywhere else on the page to save your changes.');

