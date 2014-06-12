<?php
/*
 * MluElearningFlagPlugin.php
 * Copyright (c) 2014  André Noack <noack@data-quest.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */
require dirname(__FILE__) . '/CourseAvatar.class.php';

class MluElearningFlagPlugin extends StudipPlugin implements SystemPlugin
{

    private $datafield_id = 'e450484d672cd10ed04d790e475b4c16';

    /**
     * Initialize a new instance of the plugin.
     */
    function __construct()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'dispatch.php/course/basicdata') !== false) {
            $request_uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
            $course_id = array_pop(explode('/', $request_uri));
            $course_id = Request::option('cid', $course_id);
            $course = Course::find($course_id);
            if ($course && ($df = $course->datafields->findOneBy('datafield_id', $this->datafield_id))) {
                $df = $df->getTypedDatafield();
            }
            if ($course && $df->isEditable()) {
                $snippet = '
                <h2 id="bd_description2" class="table_row_even">'
                . _("Angaben zu Elearning") .
                '</h2>
                <div style="text-align:left">
                <h3>' . formatReady($df->getDescription()) . '</h3><ul style="list-style-type:none">';
                foreach (current($df->getParams()) as $k => $v) {
                    $snippet .= '<li>';
                    $snippet .= '<label>
                    <input type="radio" name="datafields['.$df->getId().']" value="'.$k.'" '.($k == $df->getValue() ? 'checked' : '').'>
                    '.htmlReady($v).'</label>
                    </li>';
                }
                $snippet .= '</ul></div>';

                 $snippet = jsready($snippet, 'script-double');
                 PageLayout::addHeadElement('script', array('type' => 'text/javascript'),"
                     jQuery(function (\$) {\$('#settings h2').last().next('div').after('$snippet');
                     var active_accordion = jQuery('#settings').accordion('option','active');
                     jQuery('#settings').accordion('destroy').accordion({
                        active: active_accordion,
                        collapsible: true,
                        autoHeight: false,
                        change: function (event, ui) {
                        jQuery('#open_variable').attr('value', ui.newHeader.attr('id'));
                        }
                        });
                        });");
            }
        }
        if (strpos($_SERVER['REQUEST_URI'], 'dispatch.php/profile') !== false) {
            $user = User::findByUsername(Request::username('username', $GLOBALS['user']->username));
            if ($user->username == Config::get()->MLU_ELEARNING_KING) {
                $snippet = '<div>
                            <img title="KönigIn des ELearning"
                            src="'.URLHelper::getScriptUrl('plugins_packages/data-quest/MluElearningFlagPlugin/images/king_of_elearning.png').'">
                            </div>';
                $snippet = jsready($snippet, 'script-double');
                PageLayout::addHeadElement('script', array('type' => 'text/javascript'),"
                     jQuery(function (\$) {\$('#user_profile tr td h1').before('$snippet');})");
            }
        }
    }

    function getElearningCourses()
    {
        $db = DBManager::get();
        return $db->query("SELECT range_id FROM datafields_entries WHERE content > 0 AND datafield_id=" . $db->quote($this->datafield_id))->fetchAll(PDO::FETCH_COLUMN);
    }

    function checkElearningCourse($id)
    {
        $db = DBManager::get();
        $st = $db->prepare("SELECT 1 FROM datafields_entries WHERE content > 0 AND datafield_id=? AND range_id=?");
        $st->execute(array($this->datafield_id, $id));
        return $st->fetch();
    }
}