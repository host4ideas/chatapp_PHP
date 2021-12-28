CREATE database `chatapp`;
use `chatapp`;
CREATE TABLE `chats` (
  `codChat` int NOT NULL AUTO_INCREMENT,
  `chatName` varchar(25) DEFAULT "Chat",
  `chatImageURI` VARCHAR(400) NOT NULL DEFAULT "./uploads/chatsImage/default_chat_image.png/chat1.png",
  PRIMARY KEY (`codChat`)
);

CREATE TABLE `users` (
  `codUser` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(14) NOT NULL,
  `lastName` varchar(14) NOT NULL,
  `alias` varchar(14) NOT NULL,
  `email` varchar(320) NOT NULL,
  `passwd` varchar(400) NOT NULL COMMENT "For admin, the password is admin', for users the pasword is: 'Password1234-'",
  `avatarURI` VARCHAR(400) DEFAULT "./uploads/avatars/default/default_avatar.jpg",
  `actionTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `activated` tinyint(1) DEFAULT 0 COMMENT "TRUE is converted to 1 and FALSE is converted to 0",
  PRIMARY KEY (`codUser`)
);

CREATE TABLE `friends` (
  `codUser` int NOT NULL,
  `codFriend` int NOT NULL,
  `dateFriend` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`codUser`,`codFriend`),
  CONSTRAINT `fk_friends_codUser` FOREIGN KEY (`codUser`) REFERENCES `users` (`codUser`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `message` (
  `codMg` int NOT NULL AUTO_INCREMENT,
  `dateSend` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fileUri` VARCHAR(400),
  `codUser` int NOT NULL,
  `codChat` int NOT NULL,
  `textMessage` VARCHAR(400),
  `alias` varchar(14) NOT NULL,
  PRIMARY KEY (`codMg`),
  KEY `fk_message_users_codUser` (`codUser`),
  KEY `fk_message_chats_codChat` (`codChat`),
  CONSTRAINT `fk_message_chats_codChat` FOREIGN KEY (`codChat`) REFERENCES `chats` (`codChat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_message_users_codUser` FOREIGN KEY (`codUser`) REFERENCES `users` (`codUser`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `participate` (
  `codUser` int NOT NULL,
  `codChat` int NOT NULL,
  `dateEnter` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`codUser`,`codChat`),
  KEY `fk_participate_chats_codChat` (`codChat`),
  CONSTRAINT `fk_participate_chats_codChat` FOREIGN KEY (`codChat`) REFERENCES `chats` (`codChat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_participate_users_codUser` FOREIGN KEY (`codUser`) REFERENCES `users` (`codUser`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `friendrequest` (
  `codFr` int NOT NULL AUTO_INCREMENT,
  `dateSend` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted` BOOLEAN DEFAULT 0,
  `codUser` int NOT NULL,
  `codChat` int NOT NULL,
  PRIMARY KEY (`codFr`),
  KEY `fk_friendRequest_users_codUser` (`codUser`),
  KEY `fk_friendRequest_chats_codChat` (`codChat`),
  CONSTRAINT `fk_friendRequest_chats_codChat` FOREIGN KEY (`codChat`) REFERENCES `chats` (`codChat`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_friendRequest_users_codUser` FOREIGN KEY (`codUser`) REFERENCES `users` (`codUser`) ON DELETE RESTRICT ON UPDATE CASCADE
);