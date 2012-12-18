DROP TABLE IF EXISTS `message`;

CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `top_parent_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `depth` int(11) NOT NULL DEFAULT '1',
  `slug` varchar(50) NOT NULL,
  `original_message` TEXT NOT NULL,
  `message` TEXT NOT NULL,
  `status` enum('pending', 'published','deleted') NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `parent_id` (`parent_id`),
  KEY `top_parent_id` (`parent_id`)
) ENGINE=InnoDB;

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (1, 0, 0, 0, '1.', 'O que é jQuery?', 'O que é jQuery?', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (2, 0, 0, 0, '2.', 'Hora de ir pro bar', 'Hora de ir pro bar', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (3, 2, 2, 1, '2.3.', 'Vamos!!!', 'Vamos!!!', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (4, 2, 2, 1, '2.4.', 'Mas, para qual bar?', 'Mas, para qual bar?', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (5, 2, 3, 2, '2.3.5.', 'Tô nessa', 'Tô nessa', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (6, 2, 3, 2, '2.3.6.', 'Opa, também vou!', 'Opa, também vou!', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (7, 0, 0, 0, '7.', 'Você sabe quem é Mathias?', 'Você sabe quem é Mathias?', 'published');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (8, 2, 3, 2, '2.3.8.', 'Bando de cachaceiro!!!', 'Bando de cachaceiro!!!', 'deleted');

INSERT INTO `message` (id_message, top_parent_id, parent_id, depth, slug, original_message, message, status)
VALUES (9, 7, 7, 1, '7.9.', 'Num era o cara do Tropa de elite?', 'Num era o cara do Tropa de elite?', 'published');
