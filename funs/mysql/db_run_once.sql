-- -----------
-- 公用辅助性sql
-- -----------

-- -----------
-- debug sql
-- -----------
DROP TABLE IF EXISTS `t__debug`;
CREATE TABLE `t__debug`
(
    `id`  int(11) NOT NULL AUTO_INCREMENT,
    `t`   datetime    DEFAULT NULL,
    `tag` varchar(64) DEFAULT NULL,
    `msg` text        DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX ik_debug (`tag`, `t`)
) ENGINE = InnoDB
  DEFAULT CHARSET = __CHARSET__
  COLLATE __CHARSET___general_ci
    COMMENT ='通用sql调试';


-- -----------
-- 执行调试
-- -----------
DROP PROCEDURE IF EXISTS `p__debug`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    PROCEDURE `p__debug`(IN `v_tag` VARCHAR(64) CHARSET __CHARSET__,
                         IN `v_msg` text CHARSET __CHARSET__)
BEGIN
    INSERT INTO t__debug(`t`, `tag`, `msg`) VALUES (NOW(), v_tag, v_msg);
END
;;
delimiter ;

-- -----------
-- 删除过旧sql调试
-- -----------
DROP PROCEDURE IF EXISTS `p__debug_clear`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    PROCEDURE `p__debug_clear`(IN `i_days` INT)
BEGIN
    SET @sql_query = 'DELETE  FROM `t__debug` WHERE 1=1 ';
    SET @sql_query = CONCAT(@sql_query, ' AND `t` < date_add(now(), interval - ', i_days, ' day)');
    CALL p__debug('p__debug_clear', @sql_query);
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    COMMIT;
END
;;
delimiter ;


-- -----------
-- 清除旧数据，EVENT
-- -----------

DROP EVENT IF EXISTS `auto_delete_debug_log`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    EVENT `auto_delete_debug_log`
    ON SCHEDULE EVERY 1 DAY STARTS '2020-01-14 23:59:59'
    ON COMPLETION PRESERVE ENABLE
    COMMENT '自动删除30天以前的sql调试数据'
    DO
    CALL p__debug_clear(30);
;;
delimiter ;


-- -----------
-- auto_delete_debug_log end
-- -----------


-- ----------------------------
-- begin structure for p__debug_count
-- ----------------------------
DROP PROCEDURE IF EXISTS `p__debug_count`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    PROCEDURE `p__debug_count`(IN `s_tag` VARCHAR(64) CHARSET utf8mb4,
                               IN `s_date_from` VARCHAR(19),
                               IN `s_date_to` VARCHAR(19))
BEGIN

    SET @sql = 'SELECT count(`id`) AS i_count FROM `t__debug` WHERE 1=1 ';
    IF s_tag != '' THEN
        SET @sql = CONCAT(@sql, ' AND `role` =  \'', s_tag, '\' ');
    END IF;

    IF s_date_from != '' AND s_date_to != '' THEN
        SET @sql = CONCAT(@sql, ' AND (t BETWEEN \'', s_date_from, ' 00:00:00\' AND \'', s_date_to, ' 23:59:59\')');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
END;
;;
delimiter ;
-- ----------------------------
-- end structure for p__debug_count
-- ----------------------------

-- ----------------------------
-- begin structure for p__debug_list
-- ----------------------------

DROP PROCEDURE IF EXISTS `p__debug_list`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    PROCEDURE `p__debug_list`(IN `s_tag` VARCHAR(64) CHARSET utf8mb4,
                              IN `s_date_from` VARCHAR(19),
                              IN `s_date_to` VARCHAR(19),
                              IN `s_order_dir` VARCHAR(255),
                              IN `i_page` INT,
                              IN `i_page_size` INT)
BEGIN
    DECLARE m_offset INT;
    DECLARE m_length INT;
    SET m_length = i_page_size;
    SET m_offset = (i_page - 1) * i_page_size;

    SET @sql = 'SELECT * FROM `t__debug` WHERE 1=1 ';
    IF s_tag != '' THEN
        SET @sql = CONCAT(@sql, ' AND `tag` =  \'', s_tag, '\' ');
    END IF;

    IF s_date_from != '' AND s_date_to != '' THEN
        SET @sql = CONCAT(@sql, ' AND (t BETWEEN \'', s_date_from, ' 00:00:00\' AND \'', s_date_to, ' 23:59:59\')');
    END IF;
    SET @sql = CONCAT(@sql, ' ORDER BY `t` ', s_order_dir);
    SET @sql = CONCAT(@sql, ' LIMIT  ', m_offset, ',', m_length);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
END;
;;
delimiter ;
-- ----------------------------
-- end structure for p__debug_list
-- ----------------------------

-- -----------
-- sim uuid
-- -----------
DROP TABLE IF EXISTS `t__seq`;
CREATE TABLE `t__seq`
(
    `seq_name`      varchar(64)         NOT NULL,
    `seq_increment` int(11) unsigned    NOT NULL DEFAULT 1,
    `seq_min_value` int(11) unsigned    NOT NULL DEFAULT 1,
    `seq_max_value` bigint(20) unsigned NOT NULL DEFAULT 18446744073709551615,
    `seq_cur_value` bigint(20) unsigned          DEFAULT 1,
    `seq_cycle`     boolean             NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`seq_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = __CHARSET__
  COLLATE __CHARSET___general_ci
    COMMENT ='通用序列发号机';


-- -----------
-- 获取唯一值
-- -----------
DROP FUNCTION IF EXISTS `NEXT_VAL`;
delimiter ;;

CREATE FUNCTION `NEXT_VAL`(`s_seq_name` varchar(64))
    RETURNS bigint(20)
    DETERMINISTIC
BEGIN
    DECLARE cur_value BIGINT(20);
    SELECT LAST_INSERT_ID(null) INTO cur_value;
    UPDATE t__seq
    SET seq_cur_value = LAST_INSERT_ID(
            IF(
                    (seq_cur_value + seq_increment) > seq_max_value,
                    IF(
                            seq_cycle = TRUE,
                            seq_min_value,
                            NULL
                        ),
                    seq_cur_value + seq_increment
                )
        )
    WHERE seq_name = s_seq_name;
    SELECT LAST_INSERT_ID() INTO cur_value;
    RETURN cur_value;
END
;;
delimiter ;


INSERT INTO t__seq(`seq_name`)
VALUES ('uuid');
commit;

-- int task inc
DROP PROCEDURE IF EXISTS `p__uuid_create`;
delimiter ;;
CREATE
    DEFINER = __DEFINER__
    PROCEDURE `p__uuid_create`(INOUT `v_new_id` INT)
BEGIN
    DECLARE m_new_id INT;
    SELECT NEXT_VAL('uuid') INTO m_new_id;
    SELECT m_new_id INTO v_new_id;
    SELECT m_new_id AS i_new_id;

END
;;
delimiter ;




