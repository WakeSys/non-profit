-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Sep 01, 2010 at 06:31 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wakesys_clean`
--

-- --------------------------------------------------------

--
-- Table structure for table `boats`
--

CREATE TABLE IF NOT EXISTS `boats` (
  `ID` int(2) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `boatID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `boats`
--


-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `ID` int(2) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `member` tinyint(1) unsigned NOT NULL default '1',
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `credits`
--

CREATE TABLE IF NOT EXISTS `credits` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `value` decimal(6,2) unsigned NOT NULL,
  `memberID` int(6) unsigned default NULL,
  `rideID` int(6) unsigned default NULL,
  `time` timestamp NULL default CURRENT_TIMESTAMP,
  `invoiceID` int(6) unsigned default NULL,
  `payDriver` decimal(7,2) default NULL,
  `campNights` int(3) unsigned default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `credits`
--


-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `ID` int(2) unsigned NOT NULL auto_increment,
  `HTML` varchar(10) NOT NULL,
  `short` varchar(10) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`ID`, `HTML`, `short`, `active`) VALUES
(1, '&euro;', 'EUR', 1),
(5, '&yen;', 'JPY', 1),
(4, '&pound;', 'GBP', 1),
(3, 'CAD', 'CAD', 1),
(2, 'AUD', 'AUD', 1),
(6, '$', 'USD', 1),
(7, 'NZD', 'NZD', 1),
(8, 'CHF', 'CHF', 1),
(9, 'HKD', 'HKD', 1),
(10, 'SGD', 'SGD', 1),
(11, 'SEK', 'SEK', 1),
(12, 'DKK', 'DKK', 1),
(13, 'PLN', 'PLN', 1),
(14, 'NOK', 'NOK', 1),
(15, 'HUF', 'HUF', 1),
(16, 'CZK', 'CZK', 1),
(17, 'ILS', 'ILS', 1),
(18, 'MXN', 'MXN', 1),
(19, 'BRL', 'BRL', 1),
(20, 'MYR', 'MYR', 1),
(21, 'PHP', 'PHP', 1),
(22, 'TWD', 'TWD', 1),
(23, 'THB', 'THB', 1);

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE IF NOT EXISTS `information` (
  `version` decimal(4,2) NOT NULL,
  `password` varchar(32) NOT NULL,
  `lastLogin` varchar(10) default NULL,
  `active` tinyint(4) default NULL,
  `iPhoneSessionID` varchar(32) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`version`, `password`, `lastLogin`, `active`, `iPhoneSessionID`) VALUES
(0.01, 'e10adc3949ba59abbe56e057f20f883e', '1283358523', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `ID` int(6) unsigned NOT NULL auto_increment,
  `memberID` int(6) unsigned default NULL,
  `rideID` int(10) unsigned NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `value` decimal(6,2) NOT NULL,
  `loginID` int(6) NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  `paymentID` int(2) unsigned NOT NULL,
  `paymentAddPercent` decimal(5,2) NOT NULL default '0.00',
  `paymentAddValue` decimal(5,2) NOT NULL default '0.00',
  `nonMemberMail` varchar(100) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `invoices`
--


-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE IF NOT EXISTS `maintenance` (
  `ID` int(6) unsigned NOT NULL auto_increment,
  `loginID` int(6) NOT NULL,
  `boatID` int(6) unsigned NOT NULL,
  `driverID` int(6) unsigned NOT NULL,
  `rideID` int(6) unsigned default NULL,
  `ts` datetime NOT NULL,
  `fuel_liters` decimal(7,2) default NULL,
  `oil` tinyint(2) default NULL,
  `filter` tinyint(2) default NULL,
  `price` varchar(10) character set utf8 default NULL,
  `engineTime` decimal(9,2) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `maintenance`
--


-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `ID` int(3) unsigned NOT NULL auto_increment,
  `campRider` enum('yes','no','inactive') default NULL,
  `mail` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `driver` tinyint(1) unsigned NOT NULL default '2',
  `password` varchar(32) NOT NULL,
  `birthday` date NOT NULL,
  `social_security` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `postal_code` varchar(7) NOT NULL,
  `town` varchar(50) NOT NULL,
  `country` varchar(20) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `wake_cam` tinyint(2) unsigned NOT NULL,
  `ballast` enum('yes','no') NOT NULL,
  `categoryID` int(10) unsigned NOT NULL,
  `checkUser` varchar(40) NOT NULL,
  `facebookON` tinyint(2) unsigned NOT NULL,
  `facebookmail` varchar(100) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `members`
--


-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE IF NOT EXISTS `membership` (
  `ID` int(6) unsigned NOT NULL auto_increment,
  `memberID` int(6) unsigned NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `creditID` int(6) unsigned NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `membership`
--


-- --------------------------------------------------------

--
-- Table structure for table `members_lock`
--

CREATE TABLE IF NOT EXISTS `members_lock` (
  `loginID` int(5) NOT NULL,
  `riderID` int(3) NOT NULL,
  `driverID` int(3) NOT NULL,
  `boatID` int(3) unsigned NOT NULL,
  `session_id` varchar(32) character set latin1 NOT NULL,
  `lastlogin` varchar(32) character set latin1 NOT NULL,
  `rideID` int(6) unsigned NOT NULL,
  `catID` int(3) unsigned NOT NULL,
  PRIMARY KEY  (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `members_lock`
--

INSERT INTO `members_lock` (`loginID`, `riderID`, `driverID`, `boatID`, `session_id`, `lastlogin`, `rideID`, `catID`) VALUES
(-1, 0, 0, 0, '6f866ecd4d713e59c6abec161e1e1dde', '1270037441', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `ID` tinyint(2) unsigned NOT NULL auto_increment,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `name` varchar(20) NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `value` decimal(5,2) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`ID`, `active`, `name`, `percent`, `value`) VALUES
(1, 1, 'PayPal', 0.00, 0.00),
(2, 1, 'Cash', 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `paypal`
--

CREATE TABLE IF NOT EXISTS `paypal` (
  `status` varchar(100) NOT NULL,
  `ts_update` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  `mc_gross` varchar(100) default NULL,
  `protection_eligibility` varchar(100) default NULL,
  `address_status` varchar(100) default NULL,
  `payer_id` varchar(100) default NULL,
  `tax` varchar(100) default NULL,
  `address_street` varchar(100) default NULL,
  `payment_date` varchar(100) default NULL,
  `payment_status` varchar(100) default NULL,
  `charset` varchar(100) default NULL,
  `address_zip` varchar(100) default NULL,
  `first_name` varchar(100) default NULL,
  `mc_fee` varchar(100) default NULL,
  `address_country_code` varchar(100) default NULL,
  `address_name` varchar(100) default NULL,
  `notify_version` varchar(100) default NULL,
  `custom` varchar(100) default NULL,
  `payer_status` varchar(100) default NULL,
  `business` varchar(100) default NULL,
  `address_country` varchar(100) default NULL,
  `address_city` varchar(100) default NULL,
  `quantity` varchar(100) default NULL,
  `verify_sign` varchar(100) default NULL,
  `payer_email` varchar(100) default NULL,
  `txn_id` varchar(100) default NULL,
  `payment_type` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `address_state` varchar(100) default NULL,
  `receiver_email` varchar(100) default NULL,
  `payment_fee` varchar(100) default NULL,
  `receiver_id` varchar(100) default NULL,
  `txn_type` varchar(100) default NULL,
  `item_name` varchar(100) default NULL,
  `mc_currency` varchar(100) default NULL,
  `item_number` varchar(100) default NULL,
  `residence_country` varchar(100) default NULL,
  `handling_amount` varchar(100) default NULL,
  `transaction_subject` varchar(100) default NULL,
  `payment_gross` varchar(100) default NULL,
  `shipping` varchar(100) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `paypal`
--


-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
  `ID` tinyint(1) unsigned NOT NULL,
  `round` enum('down','up','round','second') NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `currencyID` tinyint(2) unsigned NOT NULL,
  `PaypalMail` varchar(50) default NULL,
  `oilchange` int(3) unsigned NOT NULL,
  `fuelingtype` enum('liters','gallons') NOT NULL,
  `payDriver` decimal(7,2) default NULL,
  `contact_name_of_school` varchar(50) NOT NULL,
  `contact_name` varchar(50) NOT NULL,
  `contact_address` varchar(50) NOT NULL,
  `contact_postal_code` varchar(50) NOT NULL,
  `contact_town` varchar(50) NOT NULL,
  `contact_country` varchar(50) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `contact_fax` varchar(50) NOT NULL,
  `contact_mail` varchar(50) NOT NULL,
  `contact_website` varchar(50) default NULL,
  `contact_BRN` varchar(50) NOT NULL,
  `contact_VAT` varchar(50) NOT NULL,
  `contact_IBAN` varchar(50) default NULL,
  `contact_BIC` varchar(50) default NULL,
  `VAT_riding` decimal(4,2) default NULL,
  `VAT_nights` decimal(4,2) default NULL,
  `VAT_fuel` decimal(4,2) default NULL,
  `VAT_deduce` enum('yes','no') NOT NULL,
  `welcome_mail` text NOT NULL,
  `welcome_mail_subject` varchar(150) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`ID`, `round`, `timezone`, `currencyID`, `PaypalMail`, `oilchange`, `fuelingtype`, `payDriver`, `contact_name_of_school`, `contact_name`, `contact_address`, `contact_postal_code`, `contact_town`, `contact_country`, `contact_phone`, `contact_fax`, `contact_mail`, `contact_website`, `contact_BRN`, `contact_VAT`, `contact_IBAN`, `contact_BIC`, `VAT_riding`, `VAT_nights`, `VAT_fuel`, `VAT_deduce`, `welcome_mail`, `welcome_mail_subject`) VALUES
(0, 'up', 'Europe/Luxembourg', 1, 'paypalpro@paypal.com', 50, 'liters', 0.10, 'Wakesystems', 'Chris Hilbert', '3, montee St Hubert', '8387', 'Koerich', 'Luxembourg', '00352691806867', '00352691806867', 'info@wakesystems.com', 'www.youthwake.lu', 'none', 'none', 'DE1111 1111 1111 1111', 'BCEELULL', 15.00, 15.00, 15.00, 'yes', 'Dear [first_name_of_member] [last_name_of_member],\r\n\r\nWelcome to [name_of_school].\r\n\r\nIn order to activate your Facebook plugin please login to Facebook and go to this address: \r\n\r\nhttp://www.facebook.com/mobile/ \r\n\r\nand write down the email address that stands under the category Upload via Email. The mail should something like this: faldfie37d4ran@m.facebook.com\r\n\r\nThen bring that email address to one of our drivers in order to enter it into our systems.\r\n\r\nBest regards,\r\nyour  [name_of_school] team', 'Welcome [first_name_of_member] [last_name_of_member] to [name_of_school]');

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE IF NOT EXISTS `prices` (
  `ID` int(3) unsigned NOT NULL auto_increment,
  `boatID` int(3) unsigned NOT NULL,
  `categoryID` tinyint(3) unsigned NOT NULL,
  `sportsID` int(3) unsigned NOT NULL,
  `price` decimal(5,2) default NULL,
  `autostop` enum('yes','no') NOT NULL default 'no',
  `preset` int(10) unsigned NOT NULL default '0',
  `member` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `boatID` (`boatID`,`categoryID`,`sportsID`,`member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `prices`
--


-- --------------------------------------------------------

--
-- Table structure for table `rides`
--

CREATE TABLE IF NOT EXISTS `rides` (
  `ID` int(6) unsigned NOT NULL auto_increment,
  `sportID` int(3) unsigned NOT NULL,
  `categoryID` int(3) unsigned NOT NULL,
  `boatID` int(3) unsigned NOT NULL,
  `driverID` int(3) unsigned NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  `riderID` int(6) NOT NULL,
  `riderName` varchar(30) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `ballast` enum('yes','no') NOT NULL,
  `priceBallast` decimal(5,2) NOT NULL default '0.00',
  `timeTotal` int(6) unsigned NOT NULL,
  `priceTotal` decimal(5,2) NOT NULL,
  `payDriver` decimal(7,2) default NULL,
  `counter` int(4) unsigned default NULL,
  `autostop` enum('yes','no') default NULL,
  `rounding` enum('up','down','round','second') NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rides`
--


-- --------------------------------------------------------

--
-- Table structure for table `rideTimes`
--

CREATE TABLE IF NOT EXISTS `rideTimes` (
  `ID` int(6) unsigned NOT NULL auto_increment,
  `rideID` int(6) unsigned NOT NULL,
  `start` varchar(20) NOT NULL,
  `stop` varchar(20) NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rideTimes`
--


-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE IF NOT EXISTS `sports` (
  `ID` int(2) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `member` tinyint(1) unsigned NOT NULL default '1',
  `nonmember` tinyint(1) unsigned NOT NULL default '1',
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sports`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
