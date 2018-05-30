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
        $this->descriptions = array();
        $this->descriptions[1] = "Die Vorlesungsinhalte werden in Form von Videoaufzeichnungen zur Verfügung gestellt.";
        $this->descriptions[2] = "Die Veranstaltung wird mit einer elektronischen Klausur (in ILIAS) abgeschlossen.";
        $this->descriptions[3] = "Zu dieser Lehrveranstaltung gibt es ein unterstützendes Online­ bzw. Multimediales Lehr­Lernangebot (bspw. der Einsatz von TED­System, Selbsttest, Wiki, Forum, Online­Aufgaben etc.).";

        if (basename($_SERVER['PHP_SELF']) == 'admin_seminare_assi.php'
            && (Request::submitted('jump_back') && Request::get('form') == 6
                || Request::submitted('jump_next') && Request::get('form') == 4)) {
            $course = new Course();
            $course->status = $_SESSION['sem_create_data']['sem_status'];
            $df = current(DatafieldEntryModel::findByModel($course, $this->datafield_id));
            if ($df) $df = $df->getTypedDatafield();
            if ($df && $df->isEditable()) {
                 $snippet = '<td width="90%" colspan="3">
                <div style="text-align:left">
                <h4>' . formatReady($df->getDescription()) . '</h4><ul style="list-style-type:none">';
                $values = $_SESSION['sem_create_data']['sem_datafields'][$this->datafield_id]['value'] ? explode('|', $_SESSION['sem_create_data']['sem_datafields'][$this->datafield_id]['value']) : array();
                foreach (current($df->getParams()) as $k => $v) {
                    if (!$k) {
                        $v = "Keine elektronischen bzw. Multimedia-Angebote";
                    }
                    $snippet .= '<li>';
                    $snippet .= '<label>
                    <input type="checkbox" name="sem_datafields['.$df->getId().'][]" value="'.$k.'" '.(in_array($k, $values) ? 'checked' : '').'>
                    '.htmlReady($v).'</label>';
                    if (isset($this->descriptions[$k])) {
                        $snippet .= '&nbsp;' . tooltipIcon($this->descriptions[$k]);
                    }
                    if (!$k) $snippet .= '<hr>';
                    $snippet .= '</li>';
                }
                $snippet .= '</ul></div></td>';

                 $snippet = jsready($snippet, 'script-double');
                 PageLayout::addHeadElement('script', array('type' => 'text/javascript'),"
                     jQuery(function (\$) {
                     \$('select[name^=\"sem_datafields[{$this->datafield_id}]\"]').closest('td').replaceWith('$snippet');
                     \$('input[name^=\"sem_datafields[{$this->datafield_id}]\"]').
                     first().on('change', function() {
                        if (this.checked) {
                            jQuery('input[name^=\"sem_datafields[{$this->datafield_id}]\"]:checked').attr('checked', false);
                            this.checked = true;
                        }
                     });
                     });");
            }
        }
        if (strpos($_SERVER['REQUEST_URI'], 'dispatch.php/course/basicdata') !== false) {
            $request_uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
            $course_id = array_pop(explode('/', $request_uri));
            $course_id = Request::option('cid', $course_id);
            $course = Course::find($course_id);
            if ($course && ($df = $course->datafields->findOneBy('datafield_id', $this->datafield_id))) {
                $df = $df->getTypedDatafield();
            }
            if ($df && $df->isEditable()) {
                $snippet = '
                <h2 id="bd_description2" class="table_row_even">'
                . htmlReady($df->getName()) .
                '</h2>
                <div style="text-align:left">
                <h3>' . formatReady($df->getDescription()) . '</h3><ul style="list-style-type:none">';
                $values = $df->getValue() ? explode('|', $df->getValue()) : array();
                foreach (current($df->getParams()) as $k => $v) {
                    if (!$k) {
                        $v = "Keine elektronischen bzw. Multimedia-Angebote";
                    }
                    $snippet .= '<li>';
                    $snippet .= '<label>
                    <input type="checkbox" name="datafields['.$df->getId().'][]" value="'.$k.'" '.(in_array($k, $values) ? 'checked' : '').'>
                    '.htmlReady($v).'</label>';
                    if (isset($this->descriptions[$k])) {
                        $snippet .= '&nbsp;' . tooltipIcon($this->descriptions[$k]);
                    }
                    if (!$k) $snippet .= '<hr>';
                    $snippet .= '</li>';
                }
                $snippet .= '</ul></div>';

                 $snippet = jsready($snippet, 'script-double');
                 PageLayout::addHeadElement('script', array('type' => 'text/javascript'),"
                     jQuery(function (\$) {
                     \$('select[name^=\"datafields[{$this->datafield_id}]\"]').attr('disabled', true).closest('tr').hide();
                     \$('#settings h2').last().next('div').after('$snippet');
                     \$('input[name^=\"datafields[{$this->datafield_id}]\"]').
                     first().on('change', function() {
                        if (this.checked) {
                            jQuery('input[name^=\"datafields[{$this->datafield_id}]\"]:checked').attr('checked', false);
                            this.checked = true;
                        }
                     });
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
            $the_kings = array_map('trim', split("[,;\n]",Config::get()->MLU_ELEARNING_KING));
            foreach($the_kings as $one) {
                list($king,$year) = explode(':', $one);
                    if ($user->username == $king) {
                    $snippet = '<div>
                                <img title="Gewinner/in des @ward ' . $year . ' - Preis f&uuml;r multimediales Lehren und Lernen"
                                src="'.URLHelper::getScriptUrl('plugins_packages/data-quest/MluElearningFlagPlugin/images/king_of_elearning_'.$year.'.png').'">
                                </div>';
                    $snippet = jsready($snippet, 'script-double');
                    PageLayout::addHeadElement('script', array('type' => 'text/javascript'),"
                         jQuery(function (\$) {\$('#user_profile tr td h1').before('$snippet');})");
                }
            }

        }
        // set up tab navigation
        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = new Navigation("ELearning Veranstaltungen");
            $navigation->setURL(PluginEngine::getURL($this));
            Navigation::addItem('/start/'.get_class($this), $navigation);
        }
    }

    function show_action()
    {
        if (!$GLOBALS['perm']->have_perm("root")) {
            $GLOBALS['perm']->perm_invalid();
        }
        $courses = Course::findMany($this->getElearningCourses(), 'ORDER BY start_time,Name');
        $captions = array(_("Nummer"), _("Name"), _("Dozenten"), _("ELearning Art"), _("Teilnehmer aktuell"), _("Semester"));
        $data = array();
        foreach ($courses as $course) {
            $row = array();
            if (!Request::submitted('download_filter')) $row[] = $course->id;
            $row[] = $course->veranstaltungsnummer;
            $row[] = $course->name;
            $row[] = join(', ', $course->members->findBy('status','dozent')->orderBy('position')->pluck('Nachname'));
            $row[] = $course->datafields->findOneBy('datafield_id', $this->datafield_id)->content;
            $row[] = count($course->members->findBy('status', array('autor','user','tutor')));
            $row[] = $course->start_semester->name;
            $data[] = $row;
        }
        if (Request::submitted('download_filter') && Request::isPost()) {
            $tmpname = md5(uniqid('tmp'));
            if (array_to_csv($data, $GLOBALS['TMP_PATH'].'/'.$tmpname, $captions)) {
                header("Location: " . GetDownloadLink($tmpname, 'elearning-veranstaltungen.csv', 4, 'force'));
                page_close();
                die();
            }
        }
        PageLayout::setTitle(_("Übersicht ELearning Veranstaltungen"));
        PageLayout::addHeadElement('script', array("src"  => URLHelper::getScriptUrl("plugins_packages/data-quest/MluElearningFlagPlugin/assets/jquery.dataTables.min.js"),
                                             "type" => "text/javascript"
                                           ));
        $template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        $template = $template_factory->open('overview.php');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox.php'));
        echo $template->render(compact('data','captions'));

    }

    function getElearningCourses()
    {
        $db = DBManager::get();
        return $db->query("SELECT range_id FROM datafields_entries WHERE content <> 0 AND datafield_id=" . $db->quote($this->datafield_id))->fetchAll(PDO::FETCH_COLUMN);
    }

    function checkElearningCourse($id)
    {
        $db = DBManager::get();
        $st = $db->prepare("SELECT 1 FROM datafields_entries WHERE content <> 0 AND datafield_id=? AND range_id=?");
        $st->execute(array($this->datafield_id, $id));
        return $st->fetch();
    }
}
