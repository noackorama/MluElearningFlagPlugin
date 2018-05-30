<?php
class InitPlugin extends DBMigration
{
    function up()
    {
        try {
        DBManager::get()->exec("ALTER TABLE `datafields` CHANGE `type` `type` ENUM('bool','textline','textarea','selectbox','date','time','email','phone','radio','combo','link','selectboxmultiple') NOT NULL DEFAULT 'textline'");
        } catch (PDOException $e){}
        DBManager::get()->exec("REPLACE INTO `datafields` (`datafield_id`, `name`, `object_type`, `object_class`, `edit_perms`, `view_perms`, `priority`, `mkdate`, `chdate`, `type`, `typeparam`, `is_required`, `description`) VALUES
('e450484d672cd10ed04d790e475b4c16', 'Einsatz elektronischer oder multimedialer Lehr­-Lernformen', 'sem', '1', 'dozent', 'user', 99, 1402497350, 1426090223, 'selectboxmultiple', '=>\r\n1=>Videoaufzeichnung der Vorlesung\r\n2=>E­-Klausur\r\n3=>Elektronisches bzw. Multimediales Lehr­-Lernangebot', 0, 'Welches elektronische bzw. Multimedia-­Angebot setzten Sie in dieser Lehrveranstaltung ein?')");
        DBManager::get()->exec("REPLACE INTO `config` (`config_id`, `parent_id`, `field`, `value`, `is_default`, `type`, `range`, `section`, `position`, `mkdate`, `chdate`, `description`, `comment`, `message_template`) VALUES (MD5('MLU_ELEARNING_KING'), '', 'MLU_ELEARNING_KING', '', '1', 'string', 'global', 'mlu', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'Nutzername(n) des/der MLU-ELearning-Königs/Könige/Königin/Königinnen', '', '')");
    }
}