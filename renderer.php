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
 * Prints a particular instance of qpractice
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_qpractice
 * @copyright  2013 Jayesh Anandani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_qpractice_renderer extends plugin_renderer_base {

    public function summary_table($sessionid) {
        global $DB;

        $session = $DB->get_record('qpractice_session', array('id' => $sessionid));
        $table = new html_table();
        $table->attributes['class'] = 'generaltable qpracticesummaryofattempt boxaligncenter';
        $table->caption = 'Hello';
        $table->head = array(get_string('totalquestions', 'qpractice'), get_string('totalmarks', 'qpractice'));
        $table->align = array('left', 'left');
        $table->size = array('', '');
        $table->data = array();
        $table->data[] = array($session->totalnoofquestions, $session->marksobtained.'/'.$session->totalmarks);
        echo html_writer::table($table);
    }

    public function summary_form($sessionid) {

        $actionurl = new moodle_url('/mod/qpractice/summary.php', array('id'=>$sessionid));
        $output='';
        $output .= html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
                'enctype' => 'multipart/form-data', 'id' => 'responseform'));
        $output .= html_writer::start_tag('div', array('align'=> 'center'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit',
                'name' => 'back', 'value' => get_string('backpractice', 'qpractice')));
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::empty_tag('input', array('type' => 'submit',
                 'name' => 'finish',    'value' => get_string('submitandfinish', 'qpractice')));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        echo $output;
    }

    public function report_table($id) {
        global $DB;
        $cm = get_coursemodule_from_id('qpractice', $id);
        $session = $DB->get_records('qpractice_session', array('qpracticeid' => $cm->instance));
        if ($session!=null) {
            $table = new html_table();
            $table->attributes['class'] = 'generaltable qpracticesummaryofpractices boxaligncenter';
            $table->caption = 'Hello';
            $table->head = array(get_string('practicedate', 'qpractice'), get_string('category', 'qpractice'),
                                get_string('typeofpractice', 'qpractice'), get_string('score', 'qpractice'),
                                get_string('timegoalset', 'qpractice'), get_string('noofquestionsattempted', 'qpractice'),
                                get_string('noofquestionsright', 'qpractice'));
            $table->align = array('left', 'left', 'left', 'left', 'left', 'left', 'left');
            $table->size = array('', '', '', '', '', '', '', '');
            $table->data = array();
            foreach ($session as $qpractice) {
                $date = $qpractice->practicedate;
                $value = $qpractice->typeofpractice;
                $categoryid = $qpractice->categoryid;

                $category = $DB->get_records_menu('question_categories', array('id'=>$categoryid), 'name');

                if ($value =='1') {
                    $value = 'Normal';
                    $timegoal = '-';

                } else if ($value == '2') {
                    $value = 'Time Achiever';
                    $timegoal = $qpractice->time;
                } else {
                    $value = 'Goal Achiever';
                    $timegoal = $qpractice->goalpercentage;
                }
                $table->data[] = array(userdate($date), $category[$categoryid], $value, $qpractice->marksobtained.'/'.$qpractice->totalmarks, $timegoal,
                                        $qpractice->totalnoofquestions, $qpractice->totalnoofquestionsright);
            }
            echo html_writer::table($table);
        } else {
            $viewurl = new moodle_url('/mod/qpractice/view.php', array('id'=>'5'));
            redirect($viewurl, 'No Records exist');
        }
    }
}