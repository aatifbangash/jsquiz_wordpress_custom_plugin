<?php
defined('ABSPATH') or die('No script kiddies please!');

//bulk delete
if(isset($_POST['bulkaction']) && $_POST['bulkaction'] == 'delete_multi') {
  $deleteIds = array();
  if(!empty($_POST['check']) && sizeof($_POST['check']) > 0) {
    foreach($_POST['check'] as $id) {
      
      $deleteIds[] = $id;

      if($id) {
        $answerTable = $wpdb->prefix . 'jsquiz_answers_tbl';
        
        $answerSql = "SELECT image FROM " . $answerTable . "
                WHERE qid = " . $id;
        $answersObj = $wpdb->get_results($answerSql);

        if($answersObj) {
          foreach($answersObj as $imgObj) {
            $imgAbsPath = $_SERVER["DOCUMENT_ROOT"] . parse_url($imgObj->image, PHP_URL_PATH);
              @unlink($imgAbsPath);
          }
          $wpdb->query("DELETE from " . $answerTable . " WHERE qid = " . $id);
        }
      }
    }
    
    $deleteSql = " DELETE FROM " . $wpdb->prefix . "jsquiz_tbl
                    WHERE qid IN(" . implode(',', $deleteIds) .") ";

    $isRecordsDeleted = $wpdb->query($deleteSql);

    if($isRecordsDeleted) {
      $msg = "Records have been deleted successfully";  
    }
  } else {
    $msg = "No record was selected";
  }
  $_SESSION['msg'] = $msg;
  echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-quiz'>";
  exit;
}

if (isset($_POST['mode']) && $_POST['mode'] == 'add') {
    global $wpdb;

    $tablename = $wpdb->prefix . 'jsquiz_tbl';

    $dataSet = array(
        'title'       => $_POST['title'],
        'slug'        => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title']))),
        'status'      => $_POST['status'],
        'startDate'   => (!empty($_POST['startDate'])) ? date('Y-m-d H:i:s', strtotime($_POST['startDate'])) : '0000-00-00 00-00-00',
        'endDate'     => (!empty($_POST['endDate'])) ? date('Y-m-d H:i:s', strtotime($_POST['endDate'])) : '0000-00-00 00-00-00',
        'gameTime'    => (int) $_POST['gameTime'],
        'dateCreated' => date('Y-m-d H:i:s', time()),
    );

    $insert = $wpdb->insert($tablename, $dataSet);

    if ($insert) {
        $_SESSION['msg'] = 'New quiz has been added successfully';
        echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-quiz'>";
        exit;
    }
}
?>
<div id="poststuff">
<form method="post" name="add_js_quiz" action="">
  <div class="" style="width:550px;" id="gallerydiv">
    <h1>Add New Quiz</h1>
    <div class="inside" style="width:550px;">
      <table class="form-table">
        <tbody>
          <tr>
            <th align="left">Quiz Title:</th>
            <th align="left"><input type="text" name="title" id="title" required="required"></th>
          </tr>
          <tr>
            <th align="left">Quiz Status</th>
            <th align="left">
              <select name="status" id="status">
                  <option value="0" selected="selected">Pending</option>
                  <option value="1">Active</option>
                </select>
            </th>
          </tr>
          <tr>
            <th colspan="2">
              <h3 class="title">Set Schedule</h3>
              <small>Set the schedule dates (start and end date) only if you want the quiz to be displayed for the specific period of time.</small>
            </th>
          </tr>
          <tr>
            <th align="left">Start Date:</th>
            <th align="left"><input type="date" name="startDate" id="startDate" ></th>
          </tr>
          <tr>
            <th align="left">End Date:</th>
            <th align="left"><input type="date" name="endDate" id="endDate" ></th>
          </tr>
          <tr>
            <th align="left">Play Timing:</th>
            <th align="left">
              <input type="number" name="gameTime" id="gameTime" value='6' > <code>(minutes)</code>
                <p class="description" id="home-description">
                  Enter the total number of minutes a quiz will be running for.
                </p>
            </th>
          </tr>
        </tbody>
      </table>
      <div class="submit">
        <input type="hidden" value="add" name="mode">
        <input type="submit" value="Add Quiz" name="addmoview" class="button-primary action">
      </div>
    </div>
  </div>
  </form>
</div>
