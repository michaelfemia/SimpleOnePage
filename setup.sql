-- phpMyAdmin SQL Dump
-- version 4.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 12, 2015 at 02:14 PM
-- Server version: 5.6.25
-- PHP Version: 5.5.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CMS`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`user_id`, `time`) VALUES
(15, '1434057760'),
(15, '1434057799'),
(15, '1434057808'),
(15, '1434057872'),
(15, '1434057907'),
(15, '1434058006'),
(15, '1434058082');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL,
  `personname` varchar(35) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `personname`, `username`, `email`, `password`, `salt`) VALUES
(15, 'Mike Femia', '5', 'michael.femia@gmail.com', '731a92be7a27897bde56fdd4fb738a5a7a07b6e1e91f2a64b69d8365f5ad904e597012dabccd28bd7e3ee8f9ded24c47ccaeeacfe91f2a233dbe14a56bb24818', '07c8ceb80e32d0d42c9edc724ef33286977cc24e67dfd657fecae4122ad1e2dcae2fb4b2b6846231e71c320e9b557337d268012ce161976d5259b98d5181961f');

-- --------------------------------------------------------

--
-- Table structure for table `tblBlockElements`
--

CREATE TABLE IF NOT EXISTS `tblBlockElements` (
  `pkBlockElementID` int(11) NOT NULL,
  `fkBlock` int(5) NOT NULL,
  `fkElementType` tinyint(2) NOT NULL,
  `fldPosition` tinyint(3) NOT NULL,
  `fldValue` varchar(5000) DEFAULT 'Value'
) ENGINE=MyISAM AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblBlockElements`
--

INSERT INTO `tblBlockElements` (`pkBlockElementID`, `fkBlock`, `fkElementType`, `fldPosition`, `fldValue`) VALUES
(90, 40, 1, 1, 'This is a page section.'),
(91, 40, 2, 3, 'Clicking MENU in the top left corner opens a set of controls to add and remove sections, edit their names, and rearrange their order. If your sections get lengthy, you can toggle them closed by clicking the section name that precedes each section editor, in bold black font.'),
(97, 40, 2, 4, 'To add child elements to a section, use the controls at the bottom of the editor (a dropdown menu with an "Add" button next to it). There are a number of text and image formatting options. Rearrange or delete these child elements with the controls to the leftâ€“ the up/down arrows and trash.'),
(99, 40, 2, 2, 'To edit headers, paragraphs, quotes, and tables, simply click the text you want to edit, and an input field will appear. Press enter/return or click anywhere else on the page to save your changes.'),
(128, 40, 6, 5, 'Text');

-- --------------------------------------------------------

--
-- Table structure for table `tblBlockElementTypes`
--

CREATE TABLE IF NOT EXISTS `tblBlockElementTypes` (
  `pkTypeID` int(11) NOT NULL,
  `fldTypeName` varchar(100) NOT NULL,
  `fldSelectName` varchar(80) NOT NULL,
  `fldContentType` tinyint(4) NOT NULL,
  `fldHTMLType` varchar(10) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblBlockElementTypes`
--

INSERT INTO `tblBlockElementTypes` (`pkTypeID`, `fldTypeName`, `fldSelectName`, `fldContentType`, `fldHTMLType`) VALUES
(1, 'blockHeadline', 'Headline', 1, 'h2'),
(2, 'blockText', 'Paragraph', 2, 'p'),
(3, 'galleryStrip', 'Filmstrip Gallery', 4, 'div'),
(4, 'table', '3- Column Table', 5, 'div'),
(5, 'largeImage', 'Full Screen Image', 3, 'div'),
(6, 'galleryLinks', 'Modal Gallery', 4, 'div'),
(7, 'parallaxParent', 'Full Parallax', 3, 'div'),
(8, 'imageArticleLink', 'Image Links', 4, 'div'),
(9, 'quote', 'Quote', 2, 'p'),
(10, 'vimeoEmbed', 'Vimeo Embed', 6, 'iframe');

-- --------------------------------------------------------

--
-- Table structure for table `tblCMS`
--

CREATE TABLE IF NOT EXISTS `tblCMS` (
  `fldEntity` varchar(35) NOT NULL,
  `fldValue` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblContactTable`
--

CREATE TABLE IF NOT EXISTS `tblContactTable` (
  `pkPersonID` int(11) NOT NULL,
  `fldTitle` varchar(30) NOT NULL,
  `fldName` varchar(50) NOT NULL,
  `fldPhone` varchar(17) DEFAULT NULL,
  `fldEmail` varchar(50) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblGalleryBlocks`
--

CREATE TABLE IF NOT EXISTS `tblGalleryBlocks` (
  `fkBlockElementID` int(15) NOT NULL,
  `fkImageID` int(15) NOT NULL,
  `fldRank` tinyint(3) NOT NULL,
  `fldCaption` varchar(300) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblHomePage`
--

CREATE TABLE IF NOT EXISTS `tblHomePage` (
  `fldEntity` varchar(35) NOT NULL,
  `fldValue` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblHomepageManagement`
--

CREATE TABLE IF NOT EXISTS `tblHomepageManagement` (
  `pkID` int(11) NOT NULL,
  `fldHTMLID` varchar(80) NOT NULL,
  `fldContentType` tinyint(1) NOT NULL,
  `fldDescription` varchar(80) NOT NULL,
  `fldValue` varchar(1000) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblImages`
--

CREATE TABLE IF NOT EXISTS `tblImages` (
  `pkImageID` int(11) NOT NULL,
  `fldImageName` varchar(200) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblPageBlocks`
--

CREATE TABLE IF NOT EXISTS `tblPageBlocks` (
  `pkPageBlockID` int(11) NOT NULL,
  `fldPageName` varchar(35) NOT NULL,
  `fldPageLink` varchar(35) NOT NULL,
  `fldNavRank` smallint(2) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblPageBlocks`
--

INSERT INTO `tblPageBlocks` (`pkPageBlockID`, `fldPageName`, `fldPageLink`, `fldNavRank`) VALUES
(40, 'New Section', 'newsection', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblPasswordReset`
--

CREATE TABLE IF NOT EXISTS `tblPasswordReset` (
  `fkMemberID` tinyint(4) NOT NULL,
  `fldVerificationCode` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblBlockElements`
--
ALTER TABLE `tblBlockElements`
  ADD PRIMARY KEY (`pkBlockElementID`);

--
-- Indexes for table `tblBlockElementTypes`
--
ALTER TABLE `tblBlockElementTypes`
  ADD PRIMARY KEY (`pkTypeID`);

--
-- Indexes for table `tblContactTable`
--
ALTER TABLE `tblContactTable`
  ADD PRIMARY KEY (`pkPersonID`);

--
-- Indexes for table `tblGalleryBlocks`
--
ALTER TABLE `tblGalleryBlocks`
  ADD UNIQUE KEY `fkBlockElement` (`fkBlockElementID`,`fkImageID`);

--
-- Indexes for table `tblHomepageManagement`
--
ALTER TABLE `tblHomepageManagement`
  ADD PRIMARY KEY (`pkID`),
  ADD UNIQUE KEY `pkID` (`pkID`);

--
-- Indexes for table `tblImages`
--
ALTER TABLE `tblImages`
  ADD PRIMARY KEY (`pkImageID`);

--
-- Indexes for table `tblPageBlocks`
--
ALTER TABLE `tblPageBlocks`
  ADD PRIMARY KEY (`pkPageBlockID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `tblBlockElements`
--
ALTER TABLE `tblBlockElements`
  MODIFY `pkBlockElementID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=129;
--
-- AUTO_INCREMENT for table `tblBlockElementTypes`
--
ALTER TABLE `tblBlockElementTypes`
  MODIFY `pkTypeID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `tblContactTable`
--
ALTER TABLE `tblContactTable`
  MODIFY `pkPersonID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `tblHomepageManagement`
--
ALTER TABLE `tblHomepageManagement`
  MODIFY `pkID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `tblImages`
--
ALTER TABLE `tblImages`
  MODIFY `pkImageID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `tblPageBlocks`
--
ALTER TABLE `tblPageBlocks`
  MODIFY `pkPageBlockID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
