<?php defined('ABSPATH') or die('No script kiddies please!');?>

<?php
if (!empty($_GET['mode']) && $_GET['mode'] == 'del') {
    $delQuizId = $_GET['qid'];
    if(empty($delQuizId)) {
    	die('invalid request');
    }

    if($delQuizId) {
		$answerTable = $wpdb->prefix . 'jsquiz_answers_tbl';
		
		$answerSql = "SELECT image FROM " . $answerTable . "
						WHERE qid = " . $delQuizId;
		$answersObj = $wpdb->get_results($answerSql);

		if($answersObj) {
			foreach($answersObj as $imgObj) {
				$imgAbsPath = $_SERVER["DOCUMENT_ROOT"] . parse_url($imgObj->image, PHP_URL_PATH);
    			@unlink($imgAbsPath);
			}
			$wpdb->query("DELETE from " . $answerTable . " WHERE qid = " . $delQuizId);
		}
	}

    $quizTable = $wpdb->prefix . 'jsquiz_tbl';
	$isQuizDeleted = $wpdb->delete( $quizTable, array( 'qid' => $delQuizId ) );

	$_SESSION['msg'] = "Quiz has been deleted successfully";
	echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-quiz'>";
    exit;
}

$searchTerm = '';
if(!empty($_POST['s'])) { // search form submit
	$searchTerm = trim($_POST['s']);
}
?>

<div id="wpbody-content">
  <div class="wrap">
    <div id="icon-nextgen-gallery" class="icon32"><br>
    </div>
    <h2>Quizzes</h2>
    <?php if (!empty($_SESSION['msg'])) {?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p>
			<strong><?php echo esc_html($_SESSION['msg']); ?>.</strong>
		</p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text">Dismiss this notice.</span>
		</button>
	</div>
	<?php unset($_SESSION['msg']); ?>
	<?php }?>
    <form class="search-form" action="./admin.php?page=list-js-quiz" method="POST">
      <p class="search-box">
        <label class="hidden" for="media-search-input">Search quiz:</label>
        <input type="text" id="media-search-input" name="s" value="<?php echo $searchTerm; ?>">
        <input type="submit" value="Search Quiz" class="button">
      </p>
    </form>
    <form id="editposts" class="nggform" method="POST" action="admin.php?page=add-js-quiz" accept-charset="utf-8">
      <div class="tablenav top">
        <div class="alignleft actions">
          <select name="bulkaction" id="bulkaction">
            <option value="no_action">Bulk actions</option>
            <option value="delete_multi">Delete</option>
          </select>
          <input type="submit" name="showThickbox" class="button-secondary" value="Apply" onclick="if ( !checkSelected() ) return false;">
          <input type="submit" name="doaction" class="button-secondary action" value="Add new quiz">
        </div>
        <div class="tablenav-pages one-page"></div>
      </div>
      <table cellspacing="0" class="wp-list-table widefat fixed striped pages">
        <thead>
          <tr>
          	<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
            	<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
            	<input type="checkbox" name="checkall" id="cb-select-all-1" onclick="checkAll(document.getElementById('editposts'));">
            </th>
            <!-- <th scope="col" id="id" class="manage-column column-id sortable asc" style="" width="35">#</th> -->
            <th scope="col" id="title" class="manage-column column-author" style=""><strong title="Quiz title">Title</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong title="Quiz status">Status</strong></th>
            <th scope="col" id="timeCls" class="manage-column column-author" style=""><strong title="Quiz play timing in minutes">Quiz Play Time</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong title="Quiz schedule start date">Start Date</strong></th>
            <th scope="col" id="end" class="manage-column column-author" style=""><strong title="Quiz expire date">End Date</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong title="Quiz created at date">Created At</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong title="Quiz created at date">Manage Answers</strong></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
            	<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
            	<input type="checkbox" name="checkall" id="cb-select-all-1" onclick="checkAll(document.getElementById('editposts'));">
            </th>
            <!-- <th scope="col" id="id" class="manage-column column-id sortable asc" style="" width="35">#</th> -->
            <th scope="col" id="title" class="manage-column column-author" style=""><strong>Title</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>Status</strong></th>
            <th scope="col" id="timeCls" class="manage-column column-author" style=""><strong>Quiz Play Time</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong>Start Date</strong></th>
            <th scope="col" id="end" class="manage-column column-author" style=""><strong>End Date</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Created At</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Manage Answers</strong></th>
          </tr>
        </tfoot>
        <tbody id="the-list">
<?php

$quizTable = $wpdb->prefix . 'jsquiz_tbl';
$query     = " SELECT * FROM " . $quizTable . " ";

if($searchTerm) { //search form submitted
	$query .= ' WHERE title LIKE "%' . strtolower($searchTerm) . '%"';
}

$items     = $wpdb->query($query);
if ($items > 0) {
	$queryStr = '';
    $p = new pagination;
    $p->items($items);
    $p->limit(30); // Limit entries per page
    $p->target("admin.php?page=" . $_GET['page']);
    if (isset($_GET['paging']) && !empty($_GET['paging'])) {
        $p->currentPage($_GET['paging']); // Gets and validates the current page
        $queryStr = '&paging=' . $_GET['paging'];
    }
    $p->calculate(); // Calculates what to show
    $p->parameterName('paging');
    $p->adjacents(1); //No. of page away from the current page

    if (!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = $_GET['paging'];
    }

    $query .= " ORDER BY qid DESC ";

    //Query for limit paging
    $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

    $query .= $limit;
    $posts   = $wpdb->get_results($query);
    $counter = 1;

    foreach ($posts as $post) {
        ?>
			            <tr>
			            	<th scope="col" class="manage-column column-cb check-column" style="">
			            		<input type="checkbox" name="check[]" onclick="checkAll(document.getElementById('editposts'));" value="<?php echo $post->qid; ?>">
			            	</th>
			            	<!-- <th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
			            		<?php echo $counter; ?>
			            	</th> -->
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top">
			            		<a href='?page=edit-js-quiz&qid=<?php echo $post->qid; ?>'>
			            			<strong><?php echo $post->title; ?></strong>
			            		</a>
			            		<div class="row-actions">
				            		<span class="edit">
				            			<a href="?page=edit-js-quiz&qid=<?php echo $post->qid; ?>">Edit</a>
				            			|
				            		</span>
				            		<span class="trash">
								        <a href="?page=list-js-quiz&mode=del&qid=<?php echo $post->qid . $queryStr; ?>" onclick="return confirm('Deleting quiz: Are you sure?');">
								            Remove
								        </a>
								    </span>
				            	</div>
			            	</th>
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
			            		<?php echo ($post->status == 1) ? 'Active' : 'Pending'; ?>
			            	</th>
				            <th scope="col" id="title" class="manage-column column-author" style="" valign="top">
				            	<?php echo $post->gameTime; ?> <i>mins</i>
				            </th>
				            <th scope="col" id="author" class="date column-date" data-colname="Date" style="" valign="top">
				            	<?php 
				            		if($post->startDate != '0000-00-00 00:00:00'){
					            		echo date('Y-m-d', strtotime($post->startDate)); 
					            	} else {
					            		echo '0000-00-00';
					            	}
				            	?>
				            </th>
				            <th scope="col" id="page_id" class="date column-date" data-colname="Date" style="" valign="top">
				            	<?php 
				            		if($post->endDate != '0000-00-00 00:00:00'){
					            		echo date('Y-m-d', strtotime($post->endDate)); 
					            	} else {
					            		echo '0000-00-00';
					            	}
				            	?>
				            </th>
				            <th scope="col" id="description" class="date column-date" data-colname="Date" style="" valign="top">
				            	<?php echo $post->dateCreated; ?>
				            </th>
				            <th scope="col" id="description" class="date column-date" data-colname="Date" style="" valign="top">
				            	<a href='?page=list-js-answers&qid=<?php echo $post->qid; ?>'>Manage</a>
				            </th>
				        </tr>
			            <?php
$counter++;
    }
    ?>
			        </tbody>
			      </table>
			      <div class="tablenav">
			      <div class="tablenav-pages one-page">
			        <div style="float:left;margin-right:40px;"><?php echo number_format($items); ?> items</div>
			        <div style="float:left" class='tablenav-pages'> <?php echo $p->show(); ?> </div>
			      </div>
			      </div>
			    </form>
			  </div>
			  <div class="clear"></div>
			</div>
			<?php
}
?>
<script type="text/javascript">

	function checkAll(form)
	{
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "check") {
					if(form.elements[i].checked == true)
						form.elements[i].checked = false;
					else
						form.elements[i].checked = true;
				}
			}
		}
	}

	function getNumChecked(form)
	{
		var num = 0;
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "check[]")
					if(form.elements[i].checked == true)
						num++;
			}
		}
		return num;
	}

	// this function check for a the number of selected images, sumbmit false when no one selected
	function checkSelected()
	{

        if (typeof document.activeElement == "undefined" && document.addEventListener) {
        	document.addEventListener("focus", function (e) {
        		document.activeElement = e.target;
        	}, true);
        }

        if ( document.activeElement.name == 'post_paged' )
            return true;

		var numchecked = getNumChecked(document.getElementById('editposts'));

		if(numchecked < 1) {
			alert('No selected');
			return false;
		}

		actionId = jQuery('#bulkaction').val();

		return confirm('You are about to start the bulk');
	}

	</script>
