<?php
class jaQuizAdminPanel
{
    public function __construct()
    {
        add_action('admin_menu', array(&$this, 'add_menu'));
    }
    // integrate the menu
    public function add_menu()
    {
        add_menu_page("Js Quizzes", "Js Quizzes", "administrator", "list-js-quiz", array(&$this, 'show_menu'),'dashicons-nametag');
        add_submenu_page("list-js-quiz", "All Js Quizzes", "All Js Quizzes", "administrator", "list-js-quiz", array(&$this, 'show_menu'));
        add_submenu_page('list-js-quiz', 'Add New Quiz', 'Add New Quiz', 'administrator', 'add-js-quiz', array(&$this, 'show_menu'));
        add_submenu_page('list-js-quiz', 'Scores', 'Scores', 'administrator', 'list-scores', array(&$this, 'show_menu'));
        add_submenu_page('list-js-quiz', '', '', 'administrator', 'edit-js-quiz', array(&$this, 'show_menu'));
        add_submenu_page("list-js-quiz", "", "", "administrator", "list-js-answers", array(&$this, 'show_menu'));
    }

    public function show_menu()
    {
        switch ($_GET['page']) {
            case "list-js-quiz":
                include_once dirname(__FILE__) . '/manage-js-quiz.php';
                $movies = new manangeJsQuiz();
                $movies->list_js_quiz();
                break;
            case "add-js-quiz":
                include_once dirname(__FILE__) . '/manage-js-quiz.php';
                $movies = new manangeJsQuiz();
                $movies->add_js_quiz();
                break;
            case "edit-js-quiz":
                include_once dirname(__FILE__) . '/manage-js-quiz.php';
                $movies = new manangeJsQuiz();
                $movies->edit_js_quiz();
                break;
            case "list-js-answers":
                include_once dirname(__FILE__) . '/manage-js-quiz.php';
                $movies = new manangeJsQuiz();
                $movies->list_js_answers();
                break;
            case "list-scores":
                include_once dirname(__FILE__) . '/manage-js-quiz.php';
                $movies = new manangeJsQuiz();
                $movies->list_scores();
                break;
            default:
                include_once dirname(__FILE__) . '/manage-movies.php';
                $movies = new manangeMovies();
                $movies->list_movies();
                break;
        }
    }
}
