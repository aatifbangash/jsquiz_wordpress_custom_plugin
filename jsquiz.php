<?php
session_start();
/*
Plugin Name: jsQuiz
Plugin URI: http://www.techfords.com
Description: Following is the Js based quiz game.
Author: Atif bangash
Version: 1.0
Author URI: http://www.techfords.com
 */

// Stop direct call
defined('ABSPATH') or die('No script kiddies please!');

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!class_exists('jsQuiz')) {
    class jsQuiz
    {
        public $jaQuizAdminPanel = '';

        public function __construct()
        {
            // $this->define_constant();
            // $this->define_tables();

            add_filter('query_vars', array(&$this, 'my_query_vars'));
            add_action('init', array(&$this, 'init_custom_rewrite'));
            add_action( 'wp_head', array(&$this, 'add_og_meta_tags' ));
            add_action('wp_ajax_record_score', array(&$this, 'record_score'));
            add_action('wp_ajax_nopriv_record_score', array(&$this, 'record_score'));
            $this->load_dependencies();
            add_filter('script_loader_tag', array(&$this, 'add_type_to_script'), 10, 3);
            
            $this->plugin_name = __FILE__;
            register_activation_hook($this->plugin_name, array(&$this, 'activate'));
            register_deactivation_hook($this->plugin_name, array(&$this, 'deactivate'));
            register_uninstall_hook($this->plugin_name, array(&$this, 'uninstall'));
        }

        public function add_og_meta_tags() 
        {
            echo '  <meta property="og:url"           content="PAGE_URL" />
                    <meta property="og:type"          content="WEB_TITLE" />
                    <meta property="og:title"         content="QUIZ_TITLE" />
                    <meta property="og:description"   content="QUIZ_DESC" />';
        }

        //ADD CUSTOM QUERY STRING:-
        public function my_query_vars($vars)
        {
            $vars[] = 'slug';
            return $vars;
        }

        #ADD CUSTOM REWRITE RULE:-

        public function init_custom_rewrite()
        {
            
            //SET PAGE ID (options.php)
            if(!get_option('_js_quiz_page_id')){
                update_option('_js_quiz_page_id', '5');
            }

            $pluginPageId = get_option( '_js_quiz_page_id', false );
            add_rewrite_rule(
                '^show-quizzes/([^/]*)/?',
                'index.php?page_id=' . $pluginPageId . '&slug=$matches[1]',
                'top');
            flush_rewrite_rules();
        }

        public function record_score()
        {
            global $wpdb;
            // The $_REQUEST contains all the data sent via ajax
            if (!empty($_REQUEST)) {
                $scoreTable = $wpdb->prefix . 'jsquiz_scores_tbl';
                $qid        = $_REQUEST['qid'];
                $title      = $_REQUEST['title'];
                $score      = $_REQUEST['score'];
                $percentage = $_REQUEST['percentage'];
                $time       = $_REQUEST['time'];
                $isGiveup   = $_REQUEST['isGiveup'];
                $winnerId   = $_REQUEST['winnerId'];
				$ip   = $_REQUEST['ip'];

                $isInserted = $wpdb->insert(
                    $scoreTable,
                    array(
                        'qid'            => $qid,
                        'title'          => $title,
                        'score'          => $score,
                        'percentScore'   => $percentage,
                        'timeToComplete' => $time,
                        'isGiveUp'       => $isGiveup,
                        'winnerId'       => $winnerId,
						'ip'             => $ip,
                        'winDate'        => date('Y-m-d H:i:s', time()),
                    )
                );
                if ($isInserted) {
                    echo 'Score Recorded Successfully.';
                }
            }

            // Always die in functions echoing ajax content
            die();
        }

        public function define_tables()
        {
            // global $wpdb;
            //$wpdb->nggpictures                    = $wpdb->prefix . 'ngg_pictures';
        }

        public function define_constant()
        {
            // define('ICELEBZ_VERSION', $this->version);
        }

        public function load_dependencies()
        {
            require_once dirname(__FILE__) . '/lib/shortcodes.php';
            $this->load_assets();

            if (is_admin()) {
                require_once dirname(__FILE__) . '/admin/admin.php';
                $this->jaQuizAdminPanel = new jaQuizAdminPanel();
            }
        }

        public function load_assets()
        {
            wp_deregister_script('jquery');
            if (is_admin()) {
                wp_enqueue_style('jsquiz-style', plugins_url('assets/css/style.css', __FILE__));
            }
			wp_enqueue_script('jquery', plugins_url('design/src/js/jquery3.1.1jquery.min.js', __FILE__));
            wp_enqueue_script('babel', 'https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.js', array(), null, true);
            wp_enqueue_style('jsquiz-design-style', plugins_url('design/src/css/game.css', __FILE__));
			wp_enqueue_script('jsquiz-animate-script', plugins_url('design/src/js/jquery.animate-colors-min.js', __FILE__));
            wp_enqueue_script('jsquiz-design-script', plugins_url('design/src/js/quiz.js', __FILE__));

        }

        public function add_type_to_script($tag, $handle, $src)
        {
            if ( 'jsquiz-design-script' !== $handle ){
                return $tag;
            }
            return str_replace( 'text/javascript', 'text/babel', $tag );
        }
        

        public function activate()
        {
            //will trigger upon plugin activation
            global $wpdb;
            include_once dirname(__FILE__) . '/admin/install.php';
            installPlugin();
        }

        public function deactivate()
        {
            //will trigger upon plugin deactivation
        }

        public function uninstall()
        {
            include_once dirname(__FILE__) . '/admin/install.php';
            uninstallPlugin();
        }
    }
    // Let's start the holy plugin
    global $jsQuiz;
    $jsQuiz = new jsQuiz();
}
