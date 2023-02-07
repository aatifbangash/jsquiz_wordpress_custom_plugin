<?php
class jaquiz_shortcodes {

    function __construct() {
        add_shortcode( 'display_js_quiz', array(&$this, 'show_js_quiz') );
        // $out = do_shortcode( '[list_js_quiz]', false );
        // echo $out;exit;
    }

    function show_js_quiz( $attrs, $content = null ) {

        global $wpdb;
        
        $slug = get_query_var( 'slug', NULL );
        $table = $wpdb->prefix . 'jsquiz_tbl';
        $out = '';
        $resultSet = null;

        extract(shortcode_atts(array( //default array
            'id'        => null,
        ), $attrs )); 
        
        if(!empty($slug)) {
            $id = $wpdb->get_var('Select qid FROM ' . $table . ' WHERE slug = "' . $slug . '"');
        } elseif (!empty($_GET['qid'])) {
            $id = (int) $_GET['qid'];
        }   

        $sql = "SELECT * FROM " . $table . " 
                    WHERE status = 1
                    AND (startDate = '0000-00-00 00:00:00' AND endDate = '0000-00-00 00:00:00') 
                            OR 
                        (startDate <= CURDATE() AND endDate >= CURDATE()) ";

        if(!empty($id)) {

            $nextLinkSql = $sql . " AND qid > " . $id . "
                                    LIMIT 1 ";
            $nextLinkRs = $wpdb->get_row($nextLinkSql);

            $randomLinkSql = $sql . " AND qid <> " . $id;
            $randomLinkSql .= " ORDER BY rand() 
                                LIMIT 1";
            $randomLinkRs = $wpdb->get_row($randomLinkSql);

            $avgScores = $wpdb->get_var("SELECT AVG(score) FROM " . $wpdb->prefix . "jsquiz_scores_tbl WHERE qid = " . $id);
            if(empty($avgScores))
                $avgScores = 0;
            
            $sql .= " AND qid = " . $id;
            $resultSet = $wpdb->get_row($sql);
            if($resultSet) {
                $answerTable = $wpdb->prefix . 'jsquiz_answers_tbl';
                $answersSql = "SELECT * FROM " . $answerTable . "
                                WHERE qid = " . $id . "
                                ORDER BY aid DESC";
                $answersResultSet = $wpdb->get_results($answersSql);
                if($answersResultSet) {
                    $quizTitle = $resultSet->title;
                    ob_start();
                    require_once WP_PLUGIN_DIR . '/jsquiz/design/index.php';
                    $out = ob_get_clean();
                }
            }
        } else {
            $sql .= ' ORDER BY qid ASC ';
            $resultSet = $wpdb->get_results($sql);
            if($resultSet) {
                $out .= '<ul>';
                foreach($resultSet as $idx => $quizObj) {
                    if(!empty($quizObj->slug)) {
                        $out .= "<li><a href='./" . $quizObj->slug . "'>" . $quizObj->title . "</a></li>";
                    } else {
                        $out .= "<li><a href='?qid=" . $quizObj->qid . "'>" . $quizObj->title . "</a></li>";
                    }
                }
                $out .= '</ul>';
            }
        }
        
        // ob_start(); $out = ob_get_clean();
        return $out;
    }

}
$jaquiz_shortcodesObj = new jaquiz_shortcodes();    

?>