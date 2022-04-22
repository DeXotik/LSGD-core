SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `aactions`;
CREATE TABLE `aactions` (
  `actionID` int NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `IP` varchar(255) NOT NULL,
  `value1` int DEFAULT NULL,
  `value2` varchar(100) DEFAULT NULL,
  `value3` varchar(100) DEFAULT NULL,
  `time` int NOT NULL,
  `accountID` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `acccomments`;
CREATE TABLE `acccomments` (
  `userID` int NOT NULL,
  `userName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `commentID` int NOT NULL,
  `timestamp` int NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `isSpam` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `registered` int NOT NULL DEFAULT '0',
  `userName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `accountID` int NOT NULL,
  `saveData` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isAdmin` int NOT NULL DEFAULT '0',
  `userID` int NOT NULL DEFAULT '0',
  `friends` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `blockedBy` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `blocked` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `mS` int NOT NULL DEFAULT '0',
  `frS` int NOT NULL DEFAULT '0',
  `cS` int NOT NULL DEFAULT '0',
  `youtubeurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitch` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `registerDate` int NOT NULL DEFAULT '0',
  `friendsCount` int NOT NULL DEFAULT '0',
  `saveKey` blob NOT NULL,
  `discordID` bigint NOT NULL DEFAULT '0',
  `discordLinkReq` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `actions`;
CREATE TABLE `actions` (
  `ID` int NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` int NOT NULL DEFAULT '0',
  `value2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value3` int NOT NULL DEFAULT '0',
  `value4` int NOT NULL DEFAULT '0',
  `value5` int NOT NULL DEFAULT '0',
  `value6` int NOT NULL DEFAULT '0',
  `account` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `bannedips`;
CREATE TABLE `bannedips` (
  `IP` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `bans`;
CREATE TABLE `bans` (
  `banID` int NOT NULL,
  `IP` varchar(15) NOT NULL,
  `accountID` int NOT NULL DEFAULT '0',
  `userName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `blocks`;
CREATE TABLE `blocks` (
  `ID` int NOT NULL,
  `person1` int NOT NULL,
  `person2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `userID` int NOT NULL,
  `userName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `levelID` int NOT NULL,
  `commentID` int NOT NULL,
  `timestamp` int NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `percent` int NOT NULL DEFAULT '0',
  `isSpam` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `cpshares`;
CREATE TABLE `cpshares` (
  `shareID` int NOT NULL,
  `levelID` int NOT NULL,
  `userID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `dailyfeatures`;
CREATE TABLE `dailyfeatures` (
  `feaID` int NOT NULL,
  `levelID` int NOT NULL,
  `timestamp` int NOT NULL,
  `type` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `friendreqs`;
CREATE TABLE `friendreqs` (
  `accountID` int NOT NULL,
  `toAccountID` int NOT NULL,
  `comment` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploadDate` int NOT NULL,
  `ID` int NOT NULL,
  `isNew` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `friendships`;
CREATE TABLE `friendships` (
  `ID` int NOT NULL,
  `person1` int NOT NULL,
  `person2` int NOT NULL,
  `isNew1` int NOT NULL,
  `isNew2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `gauntlets`;
CREATE TABLE `gauntlets` (
  `ID` int NOT NULL,
  `level1` int NOT NULL,
  `level2` int NOT NULL,
  `level3` int NOT NULL,
  `level4` int NOT NULL,
  `level5` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels` (
  `gameVersion` int NOT NULL,
  `binaryVersion` int NOT NULL DEFAULT '0',
  `userName` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `levelID` int NOT NULL,
  `levelName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `levelDesc` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `levelVersion` int NOT NULL,
  `levelLength` int NOT NULL DEFAULT '0',
  `audioTrack` int NOT NULL,
  `auto` int NOT NULL,
  `password` int NOT NULL,
  `original` int NOT NULL,
  `twoPlayer` int NOT NULL DEFAULT '0',
  `songID` int NOT NULL DEFAULT '0',
  `objects` int NOT NULL DEFAULT '0',
  `coins` int NOT NULL DEFAULT '0',
  `requestedStars` int NOT NULL DEFAULT '0',
  `extraString` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `levelString` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `levelInfo` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `secret` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `starDifficulty` int NOT NULL DEFAULT '0' COMMENT '0=N/A 10=EASY 20=NORMAL 30=HARD 40=HARDER 50=INSANE 50=AUTO 50=DEMON',
  `downloads` int NOT NULL DEFAULT '0',
  `likes` int NOT NULL DEFAULT '0',
  `starDemon` int NOT NULL DEFAULT '0',
  `starAuto` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `starStars` int NOT NULL DEFAULT '0',
  `uploadDate` varchar(1337) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `updateDate` bigint NOT NULL,
  `rateDate` bigint NOT NULL DEFAULT '0',
  `starCoins` int NOT NULL DEFAULT '0',
  `starFeatured` int NOT NULL DEFAULT '0',
  `starHall` int NOT NULL DEFAULT '0',
  `starEpic` int NOT NULL DEFAULT '0',
  `starDemonDiff` int NOT NULL DEFAULT '0',
  `userID` int NOT NULL,
  `extID` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unlisted` int NOT NULL,
  `originalReup` int NOT NULL DEFAULT '0' COMMENT 'used for levelReupload.php',
  `hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isCPShared` int NOT NULL DEFAULT '0',
  `isDeleted` int NOT NULL DEFAULT '0',
  `isLDM` int NOT NULL DEFAULT '0',
  `ban` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `levelscores`;
CREATE TABLE `levelscores` (
  `scoreID` int NOT NULL,
  `accountID` int NOT NULL,
  `levelID` int NOT NULL,
  `percent` int NOT NULL,
  `uploadDate` int NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `coins` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `ID` int NOT NULL,
  `accountID` int NOT NULL,
  `targetAccountID` int NOT NULL,
  `server` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int NOT NULL,
  `userID` int NOT NULL,
  `targetUserID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `mappacks`;
CREATE TABLE `mappacks` (
  `ID` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `levels` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'entered as "ID of level 1, ID of level 2, ID of level 3" for example "13,14,15" (without the "s)',
  `stars` int NOT NULL,
  `coins` int NOT NULL,
  `difficulty` int NOT NULL,
  `rgbcolors` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'entered as R,G,B',
  `colors2` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `userID` int NOT NULL,
  `userName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subject` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `accID` int NOT NULL,
  `messageID` int NOT NULL,
  `toAccountID` int NOT NULL,
  `timestamp` int NOT NULL,
  `secret` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `isNew` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `modactions`;
CREATE TABLE `modactions` (
  `ID` int NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value3` int NOT NULL DEFAULT '0',
  `value4` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value5` int NOT NULL DEFAULT '0',
  `value6` int NOT NULL DEFAULT '0',
  `value7` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` int NOT NULL DEFAULT '0',
  `account` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `modipperms`;
CREATE TABLE `modipperms` (
  `categoryID` int NOT NULL,
  `actionFreeCopy` int NOT NULL DEFAULT '0',
  `actionDeleteComment` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `modips`;
CREATE TABLE `modips` (
  `ID` int NOT NULL,
  `IP` varchar(69) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isMod` int NOT NULL,
  `accountID` int NOT NULL,
  `modipCategory` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `poll`;
CREATE TABLE `poll` (
  `accountID` int NOT NULL,
  `pollOption` varchar(255) NOT NULL,
  `optionID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `quests`;
CREATE TABLE `quests` (
  `ID` int NOT NULL,
  `type` int NOT NULL,
  `amount` int NOT NULL,
  `reward` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `ID` int NOT NULL,
  `levelID` int NOT NULL,
  `hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `roleassign`;
CREATE TABLE `roleassign` (
  `assignID` bigint NOT NULL,
  `roleID` bigint NOT NULL,
  `accountID` bigint NOT NULL,
  `prefix` varchar(100) DEFAULT NULL,
  `color` varchar(11) DEFAULT NULL,
  `endTime` bigint NOT NULL DEFAULT '9999999999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `roleID` bigint NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `roleName` varchar(255) NOT NULL,
  `commandRate` int NOT NULL DEFAULT '0',
  `commandFeature` int NOT NULL DEFAULT '0',
  `commandEpic` int NOT NULL DEFAULT '0',
  `commandUnepic` int NOT NULL DEFAULT '0',
  `commandVerifycoins` int NOT NULL DEFAULT '0',
  `commandDaily` int NOT NULL DEFAULT '0',
  `commandWeekly` int NOT NULL DEFAULT '0',
  `commandDelete` int NOT NULL DEFAULT '0',
  `commandSetacc` int NOT NULL DEFAULT '0',
  `commandRenameOwn` int NOT NULL DEFAULT '1',
  `commandRenameAll` int NOT NULL DEFAULT '0',
  `commandPassOwn` int NOT NULL DEFAULT '1',
  `commandPassAll` int NOT NULL DEFAULT '0',
  `commandDescriptionOwn` int NOT NULL DEFAULT '1',
  `commandDescriptionAll` int NOT NULL DEFAULT '0',
  `commandPublicOwn` int NOT NULL DEFAULT '1',
  `commandPublicAll` int NOT NULL DEFAULT '0',
  `commandUnlistOwn` int NOT NULL DEFAULT '1',
  `commandUnlistAll` int NOT NULL DEFAULT '0',
  `commandSharecpOwn` int NOT NULL DEFAULT '1',
  `commandSharecpAll` int NOT NULL DEFAULT '0',
  `commandSongOwn` int NOT NULL DEFAULT '1',
  `commandSongAll` int NOT NULL DEFAULT '0',
  `profilecommandDiscord` int NOT NULL DEFAULT '1',
  `actionRateDemon` int NOT NULL DEFAULT '0',
  `actionRateStars` int NOT NULL DEFAULT '0',
  `actionRateDifficulty` int NOT NULL DEFAULT '0',
  `actionRequestMod` int NOT NULL DEFAULT '0',
  `actionSuggestRating` int NOT NULL DEFAULT '0',
  `toolLeaderboardsban` int NOT NULL DEFAULT '0',
  `toolPackcreate` int NOT NULL DEFAULT '0',
  `toolModactions` int NOT NULL DEFAULT '0',
  `toolSuggestlist` int NOT NULL DEFAULT '0',
  `dashboardModTools` int NOT NULL DEFAULT '0',
  `modipCategory` int NOT NULL DEFAULT '0',
  `isDefault` int NOT NULL DEFAULT '0',
  `commentColor` varchar(11) NOT NULL DEFAULT '000,000,000',
  `modBadgeLevel` int NOT NULL DEFAULT '0',
  `limitMusic` int NOT NULL DEFAULT '1',
  `limitComments` int NOT NULL DEFAULT '1',
  `reward` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `songs`;
CREATE TABLE `songs` (
  `ID` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `authorID` int NOT NULL,
  `authorName` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `download` varchar(1337) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `isDisabled` int NOT NULL DEFAULT '0',
  `levelsCount` int NOT NULL DEFAULT '0',
  `reuploadTime` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `suggest`;
CREATE TABLE `suggest` (
  `ID` int NOT NULL,
  `suggestBy` int NOT NULL DEFAULT '0',
  `suggestLevelId` int NOT NULL DEFAULT '0',
  `suggestDifficulty` int NOT NULL DEFAULT '0' COMMENT '0 - NA 10 - Easy 20 - Normal 30 - Hard 40 - Harder 50 - Insane/Demon/Auto',
  `suggestStars` int NOT NULL DEFAULT '0',
  `suggestFeatured` int NOT NULL DEFAULT '0',
  `suggestAuto` int NOT NULL DEFAULT '0',
  `suggestDemon` int NOT NULL DEFAULT '0',
  `timestamp` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `suggestlevels`;
CREATE TABLE `suggestlevels` (
  `ID` int NOT NULL,
  `levelID` int NOT NULL,
  `rated` int NOT NULL DEFAULT '0',
  `timestamp` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `isRegistered` int NOT NULL,
  `userID` int NOT NULL,
  `extID` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userName` varchar(69) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'undefined',
  `stars` int NOT NULL DEFAULT '0',
  `demons` int NOT NULL DEFAULT '0',
  `icon` int NOT NULL DEFAULT '0',
  `color1` int NOT NULL DEFAULT '0',
  `color2` int NOT NULL DEFAULT '3',
  `iconType` int NOT NULL DEFAULT '0',
  `coins` int NOT NULL DEFAULT '0',
  `userCoins` int NOT NULL DEFAULT '0',
  `special` int NOT NULL DEFAULT '0',
  `gameVersion` int NOT NULL DEFAULT '0',
  `secret` varchar(69) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `accIcon` int NOT NULL DEFAULT '0',
  `accShip` int NOT NULL DEFAULT '0',
  `accBall` int NOT NULL DEFAULT '0',
  `accBird` int NOT NULL DEFAULT '0',
  `accDart` int NOT NULL DEFAULT '0',
  `accRobot` int DEFAULT '0',
  `accGlow` int NOT NULL DEFAULT '0',
  `creatorPoints` double NOT NULL DEFAULT '0',
  `IP` varchar(69) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `lastPlayed` int NOT NULL DEFAULT '0',
  `diamonds` int NOT NULL DEFAULT '0',
  `orbs` int NOT NULL DEFAULT '0',
  `completedLvls` int NOT NULL DEFAULT '0',
  `accSpider` int NOT NULL DEFAULT '0',
  `accExplosion` int NOT NULL DEFAULT '0',
  `chest1time` int NOT NULL DEFAULT '0',
  `chest2time` int NOT NULL DEFAULT '0',
  `chest1count` int NOT NULL DEFAULT '0',
  `chest2count` int NOT NULL DEFAULT '0',
  `isBanned` int NOT NULL DEFAULT '0',
  `isCreatorBanned` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `aactions`
  ADD PRIMARY KEY (`actionID`);

ALTER TABLE `acccomments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `userID` (`userID`);

ALTER TABLE `accounts`
  ADD PRIMARY KEY (`accountID`),
  ADD UNIQUE KEY `userName` (`userName`),
  ADD KEY `isAdmin` (`isAdmin`);

ALTER TABLE `actions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `type` (`type`);

ALTER TABLE `bannedips`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `bans`
  ADD PRIMARY KEY (`banID`);

ALTER TABLE `blocks`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `levelID` (`levelID`);

ALTER TABLE `cpshares`
  ADD PRIMARY KEY (`shareID`);

ALTER TABLE `dailyfeatures`
  ADD PRIMARY KEY (`feaID`);

ALTER TABLE `friendreqs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `toAccountID` (`toAccountID`);

ALTER TABLE `friendships`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `person1` (`person1`),
  ADD KEY `person2` (`person2`),
  ADD KEY `isNew1` (`isNew1`),
  ADD KEY `isNew2` (`isNew2`);

ALTER TABLE `gauntlets`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `level5` (`level5`);

ALTER TABLE `levels`
  ADD PRIMARY KEY (`levelID`),
  ADD KEY `levelID` (`levelID`),
  ADD KEY `levelName` (`levelName`),
  ADD KEY `starDifficulty` (`starDifficulty`),
  ADD KEY `starFeatured` (`starFeatured`),
  ADD KEY `starEpic` (`starEpic`),
  ADD KEY `starDemonDiff` (`starDemonDiff`),
  ADD KEY `userID` (`userID`),
  ADD KEY `likes` (`likes`),
  ADD KEY `downloads` (`downloads`),
  ADD KEY `starStars` (`starStars`),
  ADD KEY `songID` (`songID`),
  ADD KEY `audioTrack` (`audioTrack`),
  ADD KEY `levelLength` (`levelLength`),
  ADD KEY `twoPlayer` (`twoPlayer`);

ALTER TABLE `levelscores`
  ADD PRIMARY KEY (`scoreID`),
  ADD KEY `levelID` (`levelID`);

ALTER TABLE `links`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `mappacks`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `toAccountID` (`toAccountID`);

ALTER TABLE `modactions`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `modipperms`
  ADD PRIMARY KEY (`categoryID`);

ALTER TABLE `modips`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `poll`
  ADD PRIMARY KEY (`optionID`);

ALTER TABLE `quests`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `reports`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `roleassign`
  ADD PRIMARY KEY (`assignID`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleID`);

ALTER TABLE `songs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`);

ALTER TABLE `suggest`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `suggestlevels`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `userName` (`userName`),
  ADD KEY `stars` (`stars`),
  ADD KEY `demons` (`demons`),
  ADD KEY `coins` (`coins`),
  ADD KEY `userCoins` (`userCoins`),
  ADD KEY `gameVersion` (`gameVersion`),
  ADD KEY `creatorPoints` (`creatorPoints`),
  ADD KEY `diamonds` (`diamonds`),
  ADD KEY `orbs` (`orbs`),
  ADD KEY `completedLvls` (`completedLvls`),
  ADD KEY `isBanned` (`isBanned`),
  ADD KEY `isCreatorBanned` (`isCreatorBanned`);


ALTER TABLE `aactions`
  MODIFY `actionID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `acccomments`
  MODIFY `commentID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `accounts`
  MODIFY `accountID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actions`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bannedips`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bans`
  MODIFY `banID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `blocks`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `comments`
  MODIFY `commentID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `cpshares`
  MODIFY `shareID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `dailyfeatures`
  MODIFY `feaID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `friendreqs`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `friendships`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `gauntlets`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `levels`
  MODIFY `levelID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `levelscores`
  MODIFY `scoreID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `links`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `mappacks`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `messages`
  MODIFY `messageID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `modactions`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `modipperms`
  MODIFY `categoryID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `modips`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `poll`
  MODIFY `optionID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `quests`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `reports`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `roleassign`
  MODIFY `assignID` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `roles`
  MODIFY `roleID` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `songs`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `suggest`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `suggestlevels`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `userID` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
