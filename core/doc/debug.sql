-- -----------
-- debug sql  begin
-- -----------
DROP TABLE IF EXISTS `t__debug`;
CREATE TABLE `t__debug`
(
    `id`  int(11) NOT NULL AUTO_INCREMENT,
    `t`   datetime      DEFAULT NULL,
    `tag` varchar(64)   DEFAULT NULL,
    `msg` varchar(2048) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE utf8_general_ci;

DROP PROCEDURE IF EXISTS `p__debug`;
delimiter ;;
CREATE
DEFINER = `hs`@`localhost` PROCEDURE `p__debug`(IN `v_tag` VARCHAR(64) CHARSET utf8,
                                                   IN `v_msg` VARCHAR(2048) CHARSET utf8)
BEGIN
insert into t__debug(`t`, `tag`, `msg`) values (NOW(), v_tag, v_msg);
END
;;
delimiter ;

-- -----------
-- debug end
-- -----------





