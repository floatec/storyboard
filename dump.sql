DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `notes` text NOT NULL,
  `deadline` date NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `article` (`id`, `title`, `notes`, `deadline`, `created_by`) VALUES
  (1, 'test title', 'importent infos', '0000-00-00', 1);

DROP TABLE IF EXISTS `article_user`;
CREATE TABLE `article_user` (
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `article_user` (`article_id`, `user_id`) VALUES
  (1, 1),
  (7, 1);

DROP TABLE IF EXISTS `pakage`;
CREATE TABLE `pakage` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `pakage` (`id`, `article_id`, `name`) VALUES
  (1, 1, 'test pakage'),
  (2, 1, 'all about me'),
  (3, 7, 'DEFAULT');

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `pakage_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `deadline` date NOT NULL,
  `description` text NOT NULL,
  `review` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0',
  `data` longblob,
  `type` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `task` (`id`, `pakage_id`, `user_id`, `name`, `deadline`, `description`, `review`, `state`, `data`, `type`) VALUES
  (1, 1, 1, 'test', '2016-06-11', 'ttttt', 0, 0, '', 0);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id`, `username`, `password`) VALUES
  (1, 'test', 'test');


ALTER TABLE `article`
ADD PRIMARY KEY (`id`);

ALTER TABLE `pakage`
ADD PRIMARY KEY (`id`);

ALTER TABLE `task`
ADD PRIMARY KEY (`id`);


ALTER TABLE `article`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
ALTER TABLE `pakage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
ALTER TABLE `task`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;