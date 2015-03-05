-- CCBC7版本 Namido Puzzle Zero 3.0数据库结构
-- version 607
-- http://www.namido.net
--
-- 基于CCBC7.tk上的数据库在2014年8月27日实时导出的结果
-- 请用phpMyAdmin或类似工具导入服务器中。
-- 注册一个账号，在c7_user表中手动将其type改为2（出题组）或4（管理员）即可获得后台权限。
-- 服务器版本: 5.5.27
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `app_ccbc7`
--

-- --------------------------------------------------------

--
-- 表的结构 `c7_anno`
--

CREATE TABLE IF NOT EXISTS `c7_anno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `other` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_group`
--

CREATE TABLE IF NOT EXISTS `c7_group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT COMMENT '组id',
  `regtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `gname` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '组名',
  `gnum` int(11) NOT NULL DEFAULT '1' COMMENT '组队人数',
  `u0` int(11) NOT NULL COMMENT '队长uid',
  `u1` int(11) NOT NULL DEFAULT '0' COMMENT '队员1uid',
  `u2` int(11) NOT NULL DEFAULT '0' COMMENT '队员2uid',
  `u3` int(11) NOT NULL DEFAULT '0' COMMENT '队员3uid',
  `u4` int(11) NOT NULL DEFAULT '0' COMMENT '队员4uid',
  `info` text COLLATE utf8_unicode_ci COMMENT '队伍简介',
  `errortime` bigint(20) NOT NULL DEFAULT '0' COMMENT '错误时间',
  `data` text COLLATE utf8_unicode_ci COMMENT '存档',
  PRIMARY KEY (`gid`),
  KEY `gname` (`gname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='组队' AUTO_INCREMENT=205 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_imgupload`
--

CREATE TABLE IF NOT EXISTS `c7_imgupload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmid` varchar(5) NOT NULL COMMENT '题号',
  `ver` int(11) NOT NULL COMMENT '版本号',
  `oriurl` varchar(150) NOT NULL COMMENT '原始地址',
  `lnkurl` varchar(150) NOT NULL COMMENT '链接地址',
  `markpos` varchar(5) NOT NULL COMMENT '水印位置',
  PRIMARY KEY (`id`),
  KEY `tmid` (`tmid`),
  KEY `ver` (`ver`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_invite`
--

CREATE TABLE IF NOT EXISTS `c7_invite` (
  `iid` int(11) NOT NULL AUTO_INCREMENT,
  `sendtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sgid` int(11) NOT NULL,
  `duid` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `eu` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iid`),
  KEY `duid` (`duid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='组队邀请' AUTO_INCREMENT=212 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_jdbb`
--

CREATE TABLE IF NOT EXISTS `c7_jdbb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_login`
--

CREATE TABLE IF NOT EXISTS `c7_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `ltime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6234 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_mail`
--

CREATE TABLE IF NOT EXISTS `c7_mail` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `sendtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sgid` int(11) NOT NULL,
  `sgname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `suid` int(11) NOT NULL,
  `rgid` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `eu` int(11) NOT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=786 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_status`
--

CREATE TABLE IF NOT EXISTS `c7_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `gname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tmid` varchar(5) NOT NULL,
  `answer` varchar(50) NOT NULL,
  `res` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7290 ;

-- --------------------------------------------------------

--
-- 表的结构 `c7_timu`
--

CREATE TABLE IF NOT EXISTS `c7_timu` (
  `tmid` varchar(5) NOT NULL,
  `answer` varchar(50) NOT NULL,
  `md5ans` varchar(50) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tmid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `c7_user`
--

CREATE TABLE IF NOT EXISTS `c7_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `regtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL COMMENT '注册邮箱',
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '显示名称',
  `pass` varchar(120) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '用户类型',
  `info` text COLLATE utf8_unicode_ci COMMENT '用户信息',
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '所属组号',
  `data` text COLLATE utf8_unicode_ci COMMENT '存档数据',
  PRIMARY KEY (`uid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户' AUTO_INCREMENT=549 ;
