<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Next Theme uninstall function.
 *
 * @package    theme_next_theme
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author     LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Theme_next_theme uninstall function.
 *
 * @return bool
 */
function xmldb_theme_next_theme_uninstall() {
    global $CFG;

    // Clean up any theme-specific data if needed.
    return true;
}
