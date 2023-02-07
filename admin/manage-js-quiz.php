<?php
defined('ABSPATH') or die('No script kiddies please!');

class manangeJsQuiz
{
    public function list_js_quiz()
    {
        global $wpdb;
        include_once ABSPATH . '/wp-content/plugins/jsquiz/lib/pagination.class.php';
        include_once dirname(__FILE__) . '/list-js-quiz.php';

    }

    public function add_js_quiz()
    {
        global $wpdb;
        include_once dirname(__FILE__) . '/add-js-quiz.php';
    }

    public function edit_js_quiz()
    {
        global $wpdb;
        include_once dirname(__FILE__) . '/edit-js-quiz.php';
    }

    public function list_js_answers()
    {
        global $wpdb;
        include_once dirname(__FILE__) . '/list-js-answers.php';

    }

    public function list_scores()
    {
        global $wpdb;
        include_once ABSPATH . '/wp-content/plugins/jsquiz/lib/pagination.class.php';
        include_once dirname(__FILE__) . '/list-js-scores.php';

    }
}
