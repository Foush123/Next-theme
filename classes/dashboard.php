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
 * Dashboard class for Next Theme
 *
 * @package   theme_next_theme
 * @copyright 2025 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_next_theme;

defined('MOODLE_INTERNAL') || die;

/**
 * Dashboard class for Next Theme
 */
class dashboard {

    /**
     * Get dashboard statistics for the current user
     *
     * @return array
     */
    public static function get_dashboard_stats() {
        global $USER, $DB;

        $stats = [];

        // Get quiz statistics
        $quiz_stats = self::get_quiz_stats($USER->id);
        $stats['quiz'] = $quiz_stats;

        // Get time spent statistics
        $time_stats = self::get_time_stats($USER->id);
        $stats['time'] = $time_stats;

        // Get streak statistics
        $streak_stats = self::get_streak_stats($USER->id);
        $stats['streak'] = $streak_stats;

        // Get ranking statistics
        $ranking_stats = self::get_ranking_stats($USER->id);
        $stats['ranking'] = $ranking_stats;

        // Get enrollment and completion data
        $enrollment_data = self::get_enrollment_data($USER->id);
        $stats['enrollment'] = $enrollment_data;

        return $stats;
    }

    /**
     * Get quiz statistics for user
     *
     * @param int $userid
     * @return array
     */
    private static function get_quiz_stats($userid) {
        global $DB;

        // Get quiz attempts for the user
        $sql = "SELECT AVG(qa.sumgrades) as avg_score, 
                       MAX(qa.sumgrades) as max_score, 
                       MIN(qa.sumgrades) as min_score,
                       COUNT(*) as total_attempts
                FROM {quiz_attempts} qa
                JOIN {quiz} q ON q.id = qa.quiz
                WHERE qa.userid = ? AND qa.state = 'finished'";
        
        $result = $DB->get_record_sql($sql, [$userid]);
        
        if (!$result) {
            return [
                'avg_score' => 82,
                'max_score' => 92.5,
                'min_score' => 64.25,
                'change' => -10,
                'change_type' => 'negative'
            ];
        }

        // Calculate percentage scores
        $avg_score = round(($result->avg_score / 100) * 100, 1);
        $max_score = round(($result->max_score / 100) * 100, 1);
        $min_score = round(($result->min_score / 100) * 100, 1);

        return [
            'avg_score' => $avg_score,
            'max_score' => $max_score,
            'min_score' => $min_score,
            'change' => -10, // This would be calculated based on previous period
            'change_type' => 'negative'
        ];
    }

    /**
     * Get time spent statistics for user
     *
     * @param int $userid
     * @return array
     */
    private static function get_time_stats($userid) {
        global $DB;

        // Get total time spent in courses
        $sql = "SELECT SUM(timespend) as total_time
                FROM {course_completions}
                WHERE userid = ? AND timecompleted IS NOT NULL";
        
        $result = $DB->get_record_sql($sql, [$userid]);
        $total_time = $result ? $result->total_time : 0;

        // Convert to hours
        $total_hours = round($total_time / 3600, 1);

        // Get this week's time
        $week_start = strtotime('monday this week');
        $week_end = strtotime('sunday this week');
        
        $sql = "SELECT SUM(timespend) as week_time
                FROM {course_completions}
                WHERE userid = ? AND timecompleted BETWEEN ? AND ?";
        
        $result = $DB->get_record_sql($sql, [$userid, $week_start, $week_end]);
        $week_time = $result ? $result->week_time : 0;
        $week_hours = round($week_time / 3600, 1);

        return [
            'total_hours' => $total_hours,
            'week_hours' => $week_hours,
            'change' => -12.0,
            'change_type' => 'negative'
        ];
    }

    /**
     * Get streak statistics for user
     *
     * @param int $userid
     * @return array
     */
    private static function get_streak_stats($userid) {
        global $DB;

        // Get current streak (simplified - would need more complex logic)
        $current_streak = 5;
        $longest_streak = 15;

        return [
            'current_streak' => $current_streak,
            'longest_streak' => $longest_streak,
            'streak_days' => self::get_streak_days($current_streak)
        ];
    }

    /**
     * Get streak days array
     *
     * @param int $current_streak
     * @return array
     */
    private static function get_streak_days($current_streak) {
        $days = [];
        for ($i = 1; $i <= 10; $i++) {
            $days[] = [
                'day' => sprintf('%02d', $i),
                'completed' => $i <= $current_streak
            ];
        }
        return $days;
    }

    /**
     * Get ranking statistics for user
     *
     * @param int $userid
     * @return array
     */
    private static function get_ranking_stats($userid) {
        global $DB;

        // Get total number of users
        $total_users = $DB->count_records('user', ['deleted' => 0]);
        
        // Simulate ranking (would need actual ranking logic)
        $user_rank = 15;
        $total_learners = round($total_users / 1000) * 1000; // Round to nearest thousand

        return [
            'rank' => $user_rank,
            'total_learners' => $total_learners,
            'top_performers' => [
                ['name' => 'Brooklyn Simmons', 'color' => 'blue'],
                ['name' => 'Annette Black', 'color' => 'green'],
                ['name' => 'Guy Hawkins', 'color' => 'purple']
            ]
        ];
    }

    /**
     * Get enrollment and completion data for charts
     *
     * @param int $userid
     * @return array
     */
    private static function get_enrollment_data($userid) {
        global $DB;

        // Get monthly data for the past 12 months
        $months = [];
        $enrollments = [];
        $completion_rates = [];

        for ($i = 11; $i >= 0; $i--) {
            $month_start = strtotime("first day of -$i months");
            $month_end = strtotime("last day of -$i months");
            $month_name = date('M', $month_start);
            
            $months[] = $month_name;
            
            // Simulate data (would need actual queries)
            $enrollments[] = rand(100, 400);
            $completion_rates[] = rand(70, 90);
        }

        return [
            'months' => $months,
            'enrollments' => $enrollments,
            'completion_rates' => $completion_rates,
            'average' => 1250000,
            'change' => 13.4,
            'change_type' => 'positive'
        ];
    }

    /**
     * Get recent activity for user
     *
     * @param int $userid
     * @return array
     */
    public static function get_recent_activity($userid) {
        global $DB;

        $activities = [
            [
                'type' => 'course_completed',
                'title' => 'Completed "Advanced JavaScript" course',
                'time' => '2 hours ago',
                'icon' => '📚',
                'color' => 'blue'
            ],
            [
                'type' => 'badge_earned',
                'title' => 'Achieved "Quiz Master" badge',
                'time' => '1 day ago',
                'icon' => '🏆',
                'color' => 'green'
            ],
            [
                'type' => 'course_started',
                'title' => 'Started "React Fundamentals" course',
                'time' => '3 days ago',
                'icon' => '📖',
                'color' => 'purple'
            ]
        ];

        return $activities;
    }

    /**
     * Check if dashboard should be shown
     *
     * @return bool
     */
    public static function should_show_dashboard() {
        global $PAGE;
        
        // Show dashboard on frontpage and my dashboard
        return $PAGE->pagelayout === 'frontpage' || $PAGE->pagelayout === 'mydashboard';
    }
}
