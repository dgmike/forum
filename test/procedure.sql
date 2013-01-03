CREATE PROCEDURE treeItem(IN parentID INT, IN oriMessage varchar(50), IN message VARCHAR(50) )
BEGIN
  DECLARE topParent, fromOrdem, previousDepth INT;
  IF parentID IS NULL THEN
    INSERT INTO `message` (`slug`, `original_message`, `message`, `status`)
      VALUES ('X.', oriMessage, message, 'published');
    UPDATE `message` 
      SET `top_parent_id` = `id_message`
      WHERE `id_message` = LAST_INSERT_ID();
  ELSE
    SET topParent = (SELECT top_parent_id FROM `message` WHERE id_message = parentID);
    SET previousDepth = (SELECT depth FROM `message` WHERE id_message = parentID);
    SET fromOrdem = (SELECT MAX(`order`) FROM `message` WHERE parent_id = parentID);
    IF fromOrdem IS NULL THEN
      SET fromOrdem = (SELECT `order` FROM `message` WHERE id_message = parentID);
    END IF;
    UPDATE `message` SET `order` = `order` + 1 WHERE top_parent_id = topParent AND `order` > fromOrdem;
    INSERT INTO `message` (`parent_id`, `top_parent_id`, `order`, `depth`, `original_message`, `message`)
      VALUES (parentID, topParent, fromOrdem+1, IFNULL(previousDepth+1, 0), oriMessage, message);
  END IF;
END;
$$
