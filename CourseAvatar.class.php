<?php
# Lifter010: TODO

/*
 * Copyright (C) 2009 - Marcus Lunzenauer (mlunzena@uos)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */


/**
 * This class represents the avatar of a course.
 *
 * @package    studip
 * @subpackage lib
 *
 * @author    Marcus Lunzenauer (mlunzena@uos)
 * @copyright (c) Authors
 * @since     1.10
 */
class CourseAvatar extends Avatar
{

    /**
     * Returns the URL of a user's picture.
     *
     * @param    string    one of the constants Avatar::(NORMAL|MEDIUM|SMALL)
     * @param    string    an optional extension of the user's picture
     *
     * @return string    the URL to the user's picture
     */
    # TODO (mlunzena) in Url umbenennen
    function getURL($size, $ext = 'png') {
        $plugin = PluginEngine::getPlugin('MluElearningFlagPlugin');
        if ($plugin && $plugin->checkElearningCourse($this->user_id)) {
            $this->is_elearning = true;
            return sprintf('%s/%s_%s.%s',
                                     URLHelper::getScriptUrl('plugins_packages/data-quest/MluElearningFlagPlugin/images'),
                                     'elearning',
                                     $size,
                                     $ext);
        } else {
            return parent::getURL($size, $ext);
        }
    }



    /**
     * Returns an avatar object of the appropriate class.
     *
     * @param  string  the course's id
     *
     * @return mixed   the course's avatar.
     */
    static function getAvatar($course_id)
    {
        return new CourseAvatar($course_id);
    }

    /**
     * Returns an avatar object for "nobody".
     *
     * @return mixed   the course's avatar.
     */
    static function getNobody()
    {
        return new CourseAvatar('nobody');
    }

    /**
     * Returns the URL to the courses' avatars.
     *
     * @return string     the URL to the avatars
     */
    function getAvatarDirectoryUrl()
    {
        return $GLOBALS['DYNAMIC_CONTENT_URL'] . "/course";
    }


    /**
     * Returns the file system path to the courses' avatars
     *
     * @return string      the file system path to the avatars
     */
    function getAvatarDirectoryPath()
    {
        return $GLOBALS['DYNAMIC_CONTENT_PATH'] . "/course";
    }

    /**
     * Returns the CSS class to use for this avatar image.
     *
     * @param string  one of the constants Avatar::(NORMAL|MEDIUM|SMALL)
     *
     * @return string CSS class to use for the avatar
     */
    protected function getCssClass($size) {
        return sprintf('course-avatar-%s course-%s', $size, $this->user_id);
    }

    /**
     * Return the default title of the avatar.
     * @return string the default title
     */
    function getDefaultTitle()
    {
        return Seminar::GetInstance($this->user_id)->name;
    }

    /**
     * Return if avatar is visible to the current user.
     * @return boolean: true if visible
     */
    protected function checkAvatarVisibility() {
        //no special conditions for visibility of course-avatars yet
        return true;
    }

    function getImageTag($size = Avatar::MEDIUM, $opt = array()) {

        $opt['src'] = $this->getURL($size);

        if ($this->is_elearning) {
            if ($course = Course::find($this->user_id)) {
                if ($df = current(DatafieldEntryModel::findByModel($course, 'e450484d672cd10ed04d790e475b4c16'))) {
                    $df = $df->getTypedDatafield();
                    $opt['title'] = $df->getDisplayValue();
                }
            }
        }
        if (isset($opt['class'])) {
            $opt['class'] = $this->getCssClass($size) . ' ' . $opt['class'];
        } else {
            $opt['class'] = $this->getCssClass($size);
        }

        if (!isset($opt['title'])) {
            $opt['title'] = htmlReady($this->getDefaultTitle());
        }

        if (!isset($opt['alt'])) {
            $opt['alt'] = $opt['title'];
        }

        $result = '';

        foreach ($opt as $key => $value) {
            $result .= sprintf('%s="%s" ', $key, $value);
        }

        return '<img ' . $result . '>';
    }
}
