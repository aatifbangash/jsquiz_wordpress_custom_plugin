<?php defined('ABSPATH') or die('No script kiddies please!');?>
<?php
if (isset($_GET['mode']) && $_GET['mode'] == 'del') {

    global $wpdb;
    $scoreId = $_GET['sid'];

    if (!empty($scoreId)) {
        $tbl      = $wpdb->prefix . 'jsquiz_scores_tbl';
        $delWhere = array(
            'sid' => $scoreId,
        );
        $isScoreDeleted = $wpdb->delete($tbl, $delWhere);
        if ($isScoreDeleted) {
            $_SESSION['msg'] = 'Record has been deleted successfully';
            echo "<meta http-equiv='refresh' content='0;url=?page=list-scores'>";
            exit;
        }
    }
}

$searchTerm = '';
if(!empty($_POST['s'])) { // search form submit
    $searchTerm = trim($_POST['s']);
}

$order = 'desc';
if(!empty($_GET['order'])) {
    $order = $_GET['order'];
}

$orderBy = 'sid';
if(!empty($_GET['orderby'])) {
    $orderBy = $_GET['orderby'];
}

$$queryStr = '';
if (isset($_GET['paging']) && !empty($_GET['paging'])) {    
    $queryStr = '&paging=' . $_GET['paging'];
}
?>
<div id="wpbody-content">
  <div class="wrap">
    <div id="icon-nextgen-gallery" class="icon32"><br>
    </div>
    <h2>Game Scores</h2>
    <?php if (!empty($_SESSION['msg'])) {?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p>
			<strong><?php echo $_SESSION['msg']; ?>.</strong>
		</p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text">Dismiss this notice.</span>
		</button>
	</div>
	<?php unset($_SESSION['msg']);?>
	<?php }?>
    <form class="search-form" action="./admin.php?page=list-scores" method="POST">
      <p class="search-box">
        <label class="hidden" for="media-search-input">Search score:</label>
        <input type="text" id="media-search-input" name="s" value="<?php echo $searchTerm; ?>">
        <input type="submit" value="Search score" class="button">
      </p>
    </form>
    <form id="editposts" class="nggform" method="POST" action="admin.php?page=add-js-quiz" accept-charset="utf-8">
      <table cellspacing="0" class="wp-list-table widefat fixed striped pages">
        <thead>
          <tr>
            <!-- <th scope="col" id="id" class="manage-column column-id sortable asc" style="text-align: center;" width="35">#</th> -->
            <th scope="col" id="title" class="manage-column column-author" style=""><strong><a href="?page=list-scores&orderby=title<?php echo $queryStr; ?>&order=<?php echo (($order == 'desc') ? 'asc' : 'desc'); ?>">Quiz Details</a></strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>User</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong><a href="?page=list-scores&orderby=score<?php echo $queryStr; ?>&order=<?php echo (($order == 'desc') ? 'asc' : 'desc'); ?>">Score</a></strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong><a href="?page=list-scores&orderby=percentScore<?php echo $queryStr; ?>&order=<?php echo (($order == 'desc') ? 'asc' : 'desc'); ?>">Percent Score</a></strong></th>
            <th scope="col" id="timeCls" class="manage-column column-author" style=""><strong>Game Time</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong>Is Give Up</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong><a href="?page=list-scores&orderby=ip<?php echo $queryStr; ?>&order=<?php echo (($order == 'desc') ? 'asc' : 'desc'); ?>">IP</a></strong></th>
            <th scope="col" id="end" class="manage-column column-author" style=""><strong><a href="?page=list-scores&orderby=winDate<?php echo $queryStr; ?>&order=<?php echo (($order == 'desc') ? 'asc' : 'desc'); ?>">Date</a></strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Manage</strong></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <!-- <th scope="col" id="id" class="manage-column column-id sortable asc" style="text-align: center;" width="35">#</th> -->
            <th scope="col" id="title" class="manage-column column-author" style=""><strong>Quiz Details</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>User</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>Score</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>Percent Score</strong></th>
            <th scope="col" id="timeCls" class="manage-column column-author" style=""><strong>Game Time</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong>Is Give Up</strong></th>
            <th scope="col" id="start" class="manage-column column-author" style=""><strong>IP</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Date</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Manage</strong></th>
          </tr>
        </tfoot>
        <tbody id="the-list">
<?php

$scoreTable = $wpdb->prefix . 'jsquiz_scores_tbl';
$query      = " SELECT * FROM " . $scoreTable . " ";

if($searchTerm) { //search form submitted
    $query .= ' WHERE title LIKE "%' . strtolower($searchTerm) . '%"';
}

$items      = $wpdb->query($query);
if ($items > 0) {
    $queryStr = '';
    $p        = new pagination;
    $p->items($items);
    $p->limit(20); // Limit entries per page
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

    $query .= " ORDER BY {$orderBy} {$order} ";

    //Query for limit paging
    $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

    $query .= $limit;
    $posts   = $wpdb->get_results($query);
    $counter = 1;

    foreach ($posts as $post) {
        ?>
			            <tr>
			            	<!-- <th scope="col" class="author column-author" data-colname="Author" valign="top" style="text-align: center;" width="35">
			            		<?php echo $counter; ?>
			            	</th> -->
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top">
			            		<a href="?page=edit-js-quiz&qid=<?php echo $post->qid; ?>"><?php echo $post->title . ' (' . $post->qid . ')'; ?></a>
			            	</th>
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
                                <?php if($post->winnerId > 0) { ?>
                                <?php $userObj = get_user_by('id', $post->winnerId); ?>
			            		<a href="<?php echo admin_url( 'user-edit.php?user_id=' . $userObj->ID, 'http' ); ?>">
                                    <?php 
                                        echo $userObj->data->user_email;
                                    ?>
                                </a>
                                <?php } else { ?>
                                    PUBLIC QUIZ
                                <?php } ?>
                            </th>
                            <th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
			            		<?php echo $post->score; ?>
                            </th>
                            <th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
			            		<?php echo $post->percentScore; ?>
			            	</th>
				            <th scope="col" id="title" class="manage-column column-author" style="" valign="top">
				            	<?php echo $post->timeToComplete; ?>
				            </th>
				            <th scope="col" id="title" class="manage-column column-author" style="" valign="top">
				            	<?php echo ($post->isGiveUp == 1) ? 'Yes' : 'No'; ?>
				            </th>
                            <th scope="col" id="title" class="manage-column column-author" style="" valign="top">
                                <?php echo (!empty($post->ip)? $post->ip : ''); ?>
                            </th>
				            <th scope="col" id="author" class="date column-date" data-colname="Date" style="" valign="top">
				            	<?php
if ($post->winDate != '0000-00-00 00:00:00') {
            echo date('d-M-Y H:m', strtotime($post->winDate));
        } else {
            echo $post->winDate;
        }
        ?>
				            </th>
				            <th scope="col" id="description" class="date column-date" data-colname="Date" style="" valign="top">
				            	<a href='?page=list-scores&mode=del&sid=<?php echo $post->sid . $queryStr; ?>' style="color:#a00;">Remove</a>
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