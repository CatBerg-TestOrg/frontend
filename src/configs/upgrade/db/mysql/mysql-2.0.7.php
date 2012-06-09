<?php
$status = true;
$sql = <<<SQL
  CREATE TRIGGER decrement_album_photo_count AFTER DELETE ON elementAlbum
  FOR EACH ROW
    UPDATE album SET `count` = `count`-1 WHERE `id` = OLD.`album` AND `owner` = OLD.`owner`;
SQL;
$status = $status && mysql_2_0_7($sql);

$sql = <<<SQL
  CREATE TRIGGER increment_album_photo_count AFTER INSERT ON elementAlbum
  FOR EACH ROW
    UPDATE album SET `count` = `count`+1 WHERE `id` = NEW.`album` AND `owner` = NEW.`owner`;
SQL;
$status = $status && mysql_2_0_7($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_7($sql, array(':key' => 'version', ':version' => '2.0.7'));

function mysql_2_0_7($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
    return false;
  }
  return true;
}

return $status;

