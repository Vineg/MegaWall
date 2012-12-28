<?php
if(!get_svar("database_initilased")){
	my_q("
INSERT INTO `type` (`id`, `name`, `link`, `parent`, `tree`, `start`, `pub`, `user_id`, `rate`, `full_name`) VALUES
(-2, 'removed', 'removed', -1, '-2', 1, 0, 0, 0, 'removed'),
(1, 'Главная - Megawall', 'main', -1, '1', 1, 1, 0, 0.0902233, ''),
(9, 'Видео', 'video', 1, '9@1', 0, 1, 1, 0.127194, 'Видео'),
(10, 'Флуд', 'flood', 1, '10@1', 0, 1, 1, 0.0176263, ''),
(11, 'Картинки', 'images', 1, '11@1', 0, 1, 1, 0.0390048, 'Картинки'),
(1000, 'park', 'park', -1, '1000@-1', 0, 0, 0, 0, 'park'),
(1001, 'Поисковые запросы', 'search-inquirsies', 10, '1001@10@1', 0, 1, 1, 0.0268984, 'Поисковые запросы')
	");
	my_q("
INSERT INTO `theme` (`id`, `name`, `link`, `parent`, `tree`, `start`, `pub`, `user_id`, `rate`, `full_name`) VALUES
(-2, 'removed', 'removed', -1, '', 0, 1, 0, 0, ''),
(1, 'Main', 'Main', -1, '1', 1, 1, 1, 0, '')
	");
	my_q("
INSERT INTO `user` (`mail`, `login`, `pass`, `id`, `rate`, `date`, `user_page`, `session`, `remixsid`, `parent_id`, `photo`, `photo_rec`, `submit_secret`) VALUES
('none', 'System', '992166438c67a1fcbbb2e4a228680ca0', 1, 0.0205202, 1303053817, '<i>System</i>', 'a:2:{s:2:\"id\";s:1:\"3\";s:5:\"login\";s:7:\"zaoozka\";}', '3b487cf32f846528c2d7a3dbed0d1de9', 0, '', '', '')
	");
}
set_svar("database_initilased", 1);
set_svar("users_cnt", 1);