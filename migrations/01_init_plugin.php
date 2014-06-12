<?php
class InitPlugin extends DBMigration
{
    function up()
    {
        DBManager::get()->exec("INSERT IGNORE INTO `datafields` (`datafield_id`, `name`, `object_type`, `object_class`, `edit_perms`, `view_perms`, `priority`, `mkdate`, `chdate`, `type`, `typeparam`, `is_required`, `description`) VALUES
('e450484d672cd10ed04d790e475b4c16', 'Elearning', 'sem', '1', 'dozent', 'user', 99, 1402497350, 1402497492, 'selectbox', '=>\r\n1 => tolles Elearning\r\n2 => großartiges Elearning\r\n3 => umwerfendes Elearning', 0, 'Bitte geben Sie hier die Art von Elearning an, die in der Veranstaltung zum Einsatz kommt.');
");

        DBManager::get()->exec("INSERT IGNORE INTO `config` (`config_id`, `parent_id`, `field`, `value`, `is_default`, `type`, `range`, `section`, `position`, `mkdate`, `chdate`, `description`, `comment`, `message_template`) VALUES (MD5('MLU_ELEARNING_KING'), '', 'MLU_ELEARNING_KING', '', '1', 'string', 'global', 'mlu', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'Nutzernamen des MLU-ELearning-Königs/der Königin', '', '')");
    }
}