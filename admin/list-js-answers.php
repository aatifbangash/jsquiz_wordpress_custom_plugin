<?php defined('ABSPATH') or die('No script kiddies please!');?>

<?php

$quizId = (int) trim($_GET['qid']);
if (empty($quizId)) {
    die('invalid query');
}

$msg = '';
if (!empty($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$answerTable = $wpdb->prefix . 'jsquiz_answers_tbl';

// SELECTED QUIZ DETAILS
$quizSql = " SELECT * FROM " . $wpdb->prefix . "jsquiz_tbl
				WHERE qid = " . $quizId;
$quizObj = $wpdb->get_row($quizSql);

// ADD NEW ANSWER
if (!empty($_POST['mode']) && $_POST['mode'] == 'add') {
    if (!empty($_POST['answer']) && !empty($_FILES['image']['name'])) {
        $answerDataSet = array(
            'qid'         => $_POST['qid'],
            'answer'      => $_POST['answer'],
            'dateCreated' => date('Y-m-d H:i:s', time()),
        );

        //File uploading
        $allowedExts = array("jpg", "jpeg", "gif", "png");
        $tmpFile     = explode(".", $_FILES["image"]["name"]);
        $extension   = end($tmpFile);

        if ((($_FILES["image"]["type"] == "image/gif")
            || ($_FILES["image"]["type"] == "image/jpeg")
            || ($_FILES["image"]["type"] == "image/png")
            || ($_FILES["image"]["type"] == "image/pjpeg"))
            && ($_FILES["image"]["size"] < 5000000)
            && in_array($extension, $allowedExts)) {
            if ($_FILES["image"]["error"] > 0) {
                $msg = "Return Code: " . $_FILES["image"]["error"] . "<br>";
            } else {
                if (!function_exists('wp_handle_upload')) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                }

                $uploadedfile     = $_FILES['image'];
                $upload_overrides = array('test_form' => false);
                $movefile         = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {

                    $answerDataSet['image'] = $movefile['url'];

                    $answerInserted = $wpdb->insert($answerTable, $answerDataSet);
                    if ($answerInserted) {
                        $msg = "File is valid, and was successfully uploaded.\n";
                    }
                } else {
                    $msg = "Return Code: " . $movefile['error'];
                }
            }
        } else {
            $msg = "The filesize must be less than 5MB.<br />The filetype must be in jpeg, jpg, png or gif.";
        }
    } else {
        $msg = "Please fill in the required fields.";
    }
}
// END ADD NEW ANSWER

// UPDATE ANSWER
$updatetMode = false;
if (!empty($_POST['mode']) && $_POST['mode'] == 'update') {
	$updateMode = true;
	if(empty($_GET['qid']) && empty($_GET['aid'])) {
		die('Invalid request');
	}

	$updateDataSet = array(
		'answer' => $_POST['answer']
	);
	
	$updateWhere = array(
		'aid' => $_GET['aid'],
		'qid' => $_GET['qid']
	);

	if(!empty($_FILES['image']['name'])) {
		//File uploading
        $allowedExts = array("jpg", "jpeg", "gif", "png");
        $tmpFile     = explode(".", $_FILES["image"]["name"]);
        $extension   = end($tmpFile);

        if ((($_FILES["image"]["type"] == "image/gif")
            || ($_FILES["image"]["type"] == "image/jpeg")
            || ($_FILES["image"]["type"] == "image/png")
            || ($_FILES["image"]["type"] == "image/pjpeg"))
            && ($_FILES["image"]["size"] < 5000000)
            && in_array($extension, $allowedExts)) {
            if ($_FILES["image"]["error"] > 0) {
                $msg = "Return Code: " . $_FILES["image"]["error"] . "<br>";
            } else {
                if (!function_exists('wp_handle_upload')) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                }

                $uploadedfile     = $_FILES['image'];
                $upload_overrides = array('test_form' => false);
                $movefile         = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    $updateDataSet['image'] = $movefile['url'];
                } else {
                    $msg = "Return Code: " . $movefile['error'];
                }
            }
        } else {
            $msg = "The filesize must be less than 5MB.<br />The filetype must be in jpeg, jpg, png or gif.";
        }
	}

	if(empty($msg)) {
		$isUpdated = $wpdb->update( $answerTable, $updateDataSet, $updateWhere); 
		$_SESSION['msg'] = "Answer has been updated successfully";
		echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-answers&qid=" . $_GET['qid'] . "'>";
        exit;
	}
}
// END UPDATE ANSWER

// EDIT ANSWER
$editMode = false;
if (!empty($_GET['mode']) && $_GET['mode'] == 'edit') {
	$editMode = true;
	if(empty($_GET['qid']) && empty($_GET['aid'])) {
		die('Invalid request');
	}

	$answerSql = "SELECT * FROM {$answerTable} 
					WHERE aid = " . $_GET['aid'] . " 
					AND qid = " . $_GET['qid'] . "
					LIMIT 1";
	$answerObj = $wpdb->get_row($answerSql);
}
// END EDIT ANSWER

// DELETE ANSWER
if (!empty($_GET['mode']) && $_GET['mode'] == 'del') {
	if(empty($_GET['qid']) && empty($_GET['aid'])) {
		die('Invalid request');
	}
	$imageUrl = $wpdb->get_var("SELECT image FROM {$answerTable} WHERE aid = " . $_GET['aid'] . " LIMIT 1");
	$deleteWhere = array(
		'aid' => $_GET['aid'],
		'qid' => $_GET['qid']
	);

    $isDeleted = $wpdb->delete( $answerTable, $deleteWhere );
    if ($isDeleted) {
		$imgAbsPath = $_SERVER["DOCUMENT_ROOT"] . parse_url($imageUrl, PHP_URL_PATH);
    	unlink($imgAbsPath);
    	$_SESSION['msg'] = "Answer has been deleted successfully";
    	echo "<meta http-equiv='refresh' content='0;url=admin.php?page=list-js-answers&qid=" . $_GET['qid'] . "'>";
        exit;
	}
}
// END DELETE ANSWER
?>

<div id="wpbody-content">
  <div class="wrap">
    <div id="icon-nextgen-gallery" class="icon32"><br>
    </div>
    <h2><strong><?php echo ucfirst($quizObj->title); ?></strong></h2>
    <?php if (!empty($msg)) {?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p>
			<strong><?php echo $msg; ?>.</strong>
		</p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text">Dismiss this notice.</span>
		</button>
	</div>
	<?php }?>

	<div id="poststuff">
		<form method="POST" name="add_js_quiz" action="" enctype="multipart/form-data">
		  <div class="" id="gallerydiv">

		  	<?php if($editMode) { ?>
		    	<h3>Edit Answer</h3>
		    <?php } else { ?>
		    	<h3>Add Answer</h3>
		    <?php } ?>

		    <hr />
		    <div class="inside">
		      <table class="form-table">
		        <tbody>
		          <tr>
		            <th valign="top">
		            	<label for="answer">Answer*:</label>
		            	<input type="text" name="answer" id="answer" value="<?php echo (($editMode) ? $answerObj->answer : '' ); ?>" required="required">
		            	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="image">Image*:</label>
						<input type="file" name="image" id="image" <?php echo (($editMode) ? '' : 'required="required"' ); ?> >
					</th>
					<?php if($editMode) { ?>
						<th>
							<img src="<?php echo $answerObj->image; ?>" width="100" />
						</th>
					<?php } ?>
		          </tr>
		        </tbody>
		      </table>
		      <div class="submit" style="padding: 0px;">

		      	<?php if($editMode) { ?>
		      		<input type="hidden" value="update" name="mode">
		      	<?php } else { ?>
		      		<input type="hidden" value="add" name="mode">
		        <?php } ?>

		        <input type="hidden" value="<?php echo $quizObj->qid; ?>" name="qid">
		        <input type="submit" value="<?php echo (($editMode) ? 'Edit Answer' : 'Add Answer' ); ?>" name="addanswer" class="button-primary action">
		        <?php if($editMode) { ?>
		        	<a href="admin.php?page=list-js-answers&qid=<?php echo $quizObj->qid; ?>" class="button">Go Back</a>
		        <?php } else { ?>
		        	<a href="admin.php?page=edit-js-quiz&qid=<?php echo $quizObj->qid; ?>" class="button">Edit Quiz</a>
		        <?php } ?>
		      </div>
		    </div>
		  </div>
		  </form>
		</div>
<br />
<hr />
    <form id="editposts" class="nggform" method="POST" action="admin.php?page=add-js-quiz" accept-charset="utf-8">
      <table cellspacing="0" class="wp-list-table widefat fixed striped pages">
        <thead>
          <tr>
            <th scope="col" id="id" class="manage-column column-id sortable asc" style="" width="20">#</th>
            <th scope="col" id="title" class="manage-column column-author" style=""><strong>Title</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>Image</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Options</strong></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col" id="id" class="manage-column column-id sortable asc" style="" width="20">#</th>
            <th scope="col" id="title" class="manage-column column-author" style=""><strong>Title</strong></th>
            <th scope="col" id="status" class="manage-column column-author" style=""><strong>Image</strong></th>
            <th scope="col" id="date" class="manage-column column-author" style=""><strong>Options</strong></th>
          </tr>
        </tfoot>
        <tbody id="the-list">
<?php

$query = " SELECT * FROM " . $answerTable . "
				WHERE qid = " . $quizObj->qid . "
				ORDER BY aid DESC ";
$answers = $wpdb->get_results($query);

if (!empty($answers) && sizeof($answers) > 0) {
    $counter = 1;

    foreach ($answers as $answer) {
        ?>
			            <tr>
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top" width="35">
			            		<?php echo $counter; ?>
			            	</th>
			            	<th scope="col" class="author column-author" data-colname="Author" style="" valign="top">
			            		<a href='?page=list-js-answers&mode=edit&qid=<?php echo $quizObj->qid; ?>&aid=<?php echo $answer->aid; ?>'>
			            			<strong><?php echo $answer->answer; ?></strong>
			            		</a>
			            		<div class="row-actions">
				            		<span class="edit">
				            			<a href="?page=list-js-answers&mode=edit&qid=<?php echo $quizObj->qid; ?>&aid=<?php echo $answer->aid; ?>">Edit</a>
				            			|
				            		</span>
				            		<span class="trash">
								        <a href="?page=list-js-answers&mode=del&qid=<?php echo $quizObj->qid; ?>&aid=<?php echo $answer->aid; ?>" onclick="return confirm('Deleting answer: Are you sure?');">
								            Remove
								        </a>
								    </span>
				            	</div>
			            	</th>
			            	<th class="username column-username has-row-actions column-primary">
							    <img class="avatar avatar-32 photo" src="<?php echo $answer->image; ?>" width="64" />
							</th>
				            <th scope="col" id="description" class="date column-date" data-colname="Date" style="" valign="top">
				            	<div>
				            		<span class="edit">
				            			<a href="?page=list-js-answers&mode=edit&qid=<?php echo $quizObj->qid; ?>&aid=<?php echo $answer->aid; ?>">Edit</a>
				            			|
				            		</span>
				            		<span class="trash">
								        <a href="?page=list-js-answers&mode=del&qid=<?php echo $quizObj->qid; ?>&aid=<?php echo $answer->aid; ?>" style="color:#a00;" onclick="return confirm('Deleting answer: Are you sure?');">
								            Remove
								        </a>
								    </span>
				            	</div>
				            </th>
				        </tr>
			            <?php
$counter++;
    }
    ?>
			        </tbody>
			      </table>
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
