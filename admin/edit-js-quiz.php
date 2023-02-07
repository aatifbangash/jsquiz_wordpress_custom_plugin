<?php
defined('ABSPATH') or die('No script kiddies please!');

global $wpdb;
$quizId    = (int) $_GET['qid'];
$quizTable = $wpdb->prefix . 'jsquiz_tbl';

if (isset($_POST['mode']) && $_POST['mode'] == 'edit') {

    $dataSet = array(
      'title' => $_POST['title'],
      'status' => $_POST['status'],
      'startDate' => (!empty($_POST['startDate'])) ? date('Y-m-d H:i:s', strtotime($_POST['startDate'])) : '0000-00-00 00-00-00',
      'endDate' => (!empty($_POST['endDate'])) ? date('Y-m-d H:i:s', strtotime($_POST['endDate'])) : '0000-00-00 00-00-00',
      'gameTime' => $_POST['gameTime']
    );
    $updated = $wpdb->update(
        $quizTable,
        $dataSet,
        array('qid' => $quizId)
    );

    if ($updated) {
        $_SESSION['msg'] = "Quiz has been updated successfully";
        echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-quiz'>";
        exit;
    }
}

$quizSql = " SELECT * FROM " . $quizTable . "
                  WHERE qid = " . $quizId;

$quizObj = $wpdb->get_row($quizSql);
if (empty($quizObj)) {
    die('invalid query.');
}

?>
<div id="poststuff">
<form method="post" name="add_js_quiz" action="">
  <div class="" style="width:550px;" id="gallerydiv">
    <h1>Edit New Quiz</h1>
    <div class="inside" style="width:550px;">
      <table class="form-table">
        <tbody>
          <tr>
            <th align="left">Quiz Title:</th>
            <th align="left"><input type="text" name="title" id="title" required="required" value="<?php echo $quizObj->title; ?>"></th>
          </tr>
          <tr>
            <th align="left">Quiz Slug:</th>
            <th align="left">
              <input type="text" name="slug" id="slug" disabled="disabled" style="color:black;" value="<?php echo $quizObj->slug; ?>">
              <p class="description" id="home-description">
                  Slug is used as an SEO friendly link.
                </p>
            </th>

          </tr>
          <tr>
            <th align="left">Quiz Status</th>
            <th align="left">
              <select name="status" id="status">
                  <option value="0" <?php if ($quizObj->status == 0) {echo 'selected';}?> >Pending</option>
                  <option value="1" <?php if ($quizObj->status == 1) {echo 'selected';}?> >Active</option>
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
            <th align="left"><input type="date" name="startDate" id="startDate" value="<?php echo (($quizObj->startDate != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($quizObj->startDate)) : '') ; ?>" ></th>
          </tr>
          <tr>
            <th align="left">End Date:</th>
            <th align="left"><input type="date" name="endDate" id="endDate" value="<?php echo (($quizObj->endDate != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($quizObj->endDate)) : '') ; ?>" ></th>
          </tr>
          <tr>
            <th align="left">Play Timing:</th>
            <th align="left">
              <input type="number" name="gameTime" id="gameTime" value='<?php echo $quizObj->gameTime; ?>' > <code>(minutes)</code>
                <p class="description" id="home-description">
                  Enter the total number of minutes a quiz will be running for.
                </p>
            </th>
          </tr>
        </tbody>
      </table>
      <div class="submit">
        <input type="hidden" value="edit" name="mode">
        <input type="hidden" value="<?php echo $quizObj->qid; ?>" name="qid">
        <input type="submit" value="Update Quiz" name="addquiz" class="button-primary action">
        <a href="admin.php?page=list-js-answers&qid=<?php echo $quizObj->qid; ?>" class="button">Manage Answers</a>
      </div>
    </div>
  </div>
  </form>
</div>