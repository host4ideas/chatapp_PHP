INSERT INTO users VALUES(1, "admin", "admin", "admin", "admin@admin.com", "$2y$10$HTkur1cOWj3MjPwIJxzl.eDINpyRXgE1nqZytqcYPe1vbEidEO4cm", "./uploads/avatars/default/default_avatar.jpg", null, 1);
INSERT INTO users VALUES(2, "testUserOne", "testUserOne", "aliasTestOne", "example@example1.es", "$2y$10$0UksxQd.AfcjBwZP1cNgP.aLRLFBZbflOlqdv5TQg7TOpmG4EZngi", "./uploads/avatars/image1.png", null, 1);
INSERT INTO users VALUES(3, "testUserTwo", "testUserTwo", "aliasTestTwo", "example@example2.es", "$2y$10$WAe1CFGJIIc/QMtazTKmXezFmwLlJ/GJkLQG/cFb3GTaldl9HW5.W", "./uploads/avatars/image2.png", null, 1);
INSERT INTO users VALUES(4, "userTestThree", "userTestThree", "aliasTestThree", "example@example3.es", "$2y$10$7KT5fKhPxbk4f9bHrMbzXeF72yO2vlh1rbbeHGAmHFS.QMcbtuN.2", "./uploads/avatars/image1.png", null, 1);
INSERT INTO users VALUES(5, "userTestFour", "userTestFour", "aliasTestFour", "example@example4.es", "$2y$10$snVH8lA/EypmVuBN9YBM4.NQfDA5MfU8cH1AQJFB2jRFHkCaJOGbW", "./uploads/avatars/image2.png", null, 1);

INSERT INTO `chats` (`codChat`, `chatName`, `chatImageURI`) VALUES
(1, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png'),
(3, 'My New Group', './uploads/chatsImage/default_chat_image/chat1.png'),
(4, 'Friend request', './uploads/chatsImage/default_chat_image/friend_request.jpg'),
(6, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png'),
(7, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png'),
(8, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png'),
(12, 'Friend request', './uploads/chatsImage/default_chat_image/friend_request.jpg');

INSERT INTO `friendrequest` (`codFr`, `dateSend`, `accepted`, `codUser`, `codChat`) VALUES
(1, '2021-11-24 09:33:31', 0, 4, 3),
(6, '2021-11-24 11:06:39', 0, 5, 12);

INSERT INTO `message` (`codMg`, `dateSend`, `fileUri`, `codUser`, `codChat`, `textMessage`, `alias`) VALUES
(1, '2021-11-24 09:33:31', NULL, 2, 4, '<a class=\"btn btn-primary\" href=\"../friend_request.php?id=2&fr=1&accepted=true&chat=4\" role=\"button\">Accept</a><a class=\"btn btn-danger\" href=\"../friend_request.php?id=2&fr=1&accepted=false&chat=4\" role=\"button\">Reject</a>', 'aliasTestOne'),
(3, '2021-11-24 10:13:09', '../uploads/attachments/image2.png', 4, 6, 'Test broadcast', 'aliasTestThree'),
(4, '2021-11-24 10:13:09', '../uploads/attachments/image2.png', 4, 7, 'Test broadcast', 'aliasTestThree'),
(5, '2021-11-24 10:13:09', '../uploads/attachments/image2.png', 4, 8, 'Test broadcast', 'aliasTestThree'),
(23, '2021-11-24 11:06:39', NULL, 4, 12, '<a class=\"btn btn-primary\" href=\"../friend_request.php?id=4&fr=6&accepted=true&chat=12\" role=\"button\">Accept</a><a class=\"btn btn-danger\" href=\"../friend_request.php?id=4&fr=6&accepted=false&chat=12\" role=\"button\">Reject</a>', 'aliasTestThree'),
(24, '2021-11-24 11:15:29', NULL, 2, 1, 'Test unread message', 'aliasTestOne'),
(25, '2021-11-24 11:15:36', NULL, 2, 6, 'Test unread message', 'aliasTestOne'),
(26, '2021-11-24 11:15:54', '../uploads/attachments/Horarios DW2E.pdf', 2, 3, 'Test unread message with pdf', 'aliasTestOne');

INSERT INTO `participate` (`codUser`, `codChat`, `dateEnter`) VALUES
(2, 1, '2021-11-24 11:15:30'),
(2, 3, '2021-11-24 11:15:41'),
(2, 6, '2021-11-24 11:15:41'),
(3, 1, '2021-11-24 09:31:43'),
(3, 3, '2021-11-24 09:33:16'),
(3, 4, '2021-11-24 10:19:24'),
(3, 7, '2021-11-24 10:13:09'),
(4, 3, '2021-11-24 10:41:30'),
(4, 6, '2021-11-24 10:41:31'),
(4, 7, '2021-11-24 11:13:21'),
(4, 8, '2021-11-24 11:14:21'),
(5, 3, '2021-11-24 11:14:41'),
(5, 8, '2021-11-24 11:09:40'),
(5, 12, '2021-11-24 11:09:40');

INSERT INTO `users` (`codUser`, `firstName`, `lastName`, `alias`, `email`, `passwd`, `avatarURI`, `actionTime`, `activated`) VALUES
(1, 'admin', 'admin', 'admin', 'admin@admin.com', '$2y$10$HTkur1cOWj3MjPwIJxzl.eDINpyRXgE1nqZytqcYPe1vbEidEO4cm', NULL, NULL, 1),
(2, 'testUserOne', 'testUserOne', 'aliasTestOne', 'example@example1.es', '$2y$10$0UksxQd.AfcjBwZP1cNgP.aLRLFBZbflOlqdv5TQg7TOpmG4EZngi', './uploads/avatars/image1.png', NULL, 1),
(3, 'testUserTwo', 'testUserTwo', 'aliasTestTwo', 'example@example2.es', '$2y$10$WAe1CFGJIIc/QMtazTKmXezFmwLlJ/GJkLQG/cFb3GTaldl9HW5.W', './uploads/avatars/image2.png', NULL, 1),
(4, 'userTestThree', 'userTestThree', 'aliasTestThree', 'example@example3.es', '$2y$10$7KT5fKhPxbk4f9bHrMbzXeF72yO2vlh1rbbeHGAmHFS.QMcbtuN.2', './uploads/avatars/image1.png', NULL, 1),
(5, 'userTestFour', 'userTestFour', 'aliasTestFour', 'example@example4.es', '$2y$10$snVH8lA/EypmVuBN9YBM4.NQfDA5MfU8cH1AQJFB2jRFHkCaJOGbW', './uploads/avatars/image2.png', NULL, 1);

