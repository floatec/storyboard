
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `notes` text NOT NULL,
  `deadline` date NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `article` (`id`, `title`, `notes`, `deadline`, `created_by`) VALUES
  (1, 'test title', 'importent infos', '0000-00-00', 1),
  (2, 'test', 'blafoo', '2016-06-15', 1),
  (3, 'all about me', 'non', '2016-07-01', 1),
  (4, 'all about me', 'non', '2016-07-01', 1),
  (5, 'all about me', 'non', '2016-07-01', 1),
  (6, 'all about me', 'non', '2016-07-01', 1);

DROP TABLE IF EXISTS `article_user`;
CREATE TABLE `article_user` (
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `article_user` (`article_id`, `user_id`) VALUES
  (1, 1);

DROP TABLE IF EXISTS `pakage`;
CREATE TABLE `pakage` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `pakage` (`id`, `article_id`, `name`) VALUES
  (1, 1, 'test pakage'),
  (2, 1, 'all about me');

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `pakage_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `deadline` date NOT NULL,
  `description` text NOT NULL,
  `review` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `task` (`id`, `pakage_id`, `user_id`, `name`, `deadline`, `description`, `review`) VALUES
  (1, 1, 1, 'test', '2016-06-11', 'ttttt', 0);

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
ALTER TABLE `pakage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
ALTER TABLE `task`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;