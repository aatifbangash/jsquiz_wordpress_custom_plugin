<!-- Load Facebook SDK for JavaScript -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
<div id="MainViewable">
    <div id="bg-temp">
        <div id="page-wrapper">
            <div id="CenterContent">
                <div class="showIcon" id="gameMeta">
                    <h2>
                        <?php echo ucfirst($quizTitle); ?>
                    </h2>
                </div>
                <div id="gameHeaderWrapper">
                    <div id="gameBarBox">
                        <div id="timerScoreBox">
                            <span class="smallLine" id="scoreBox">
                                <span class="timerScoreControls">
                                    <span class="timerScoreTitle">
                                        Score
                                    </span>
                                    <span class="timerScoreSettings">
                                        <ul class="dropdown" style="display:none;">
                                            <li class="active" value="numeric">
                                                Numerical
                                            </li>
                                            <li value="percentage">
                                                Percentage
                                            </li>
                                        </ul>
                                    </span>
                                </span>
                                <span class="currentScore" id="currentScore">
                                </span>
                            </span>
                            <span id="pauseBox" onclick="pauseGame();" style="">
                                <!--<a href="#" onclick="return false;">ll</a>-->
                            </span>
                            <span class="smallLine" id="timeBox">
                                <span class="timerScoreControls">
                                    <span class="timerScoreTitle">
                                        Timer
                                    </span>
                                    <span class="timerScoreSettings">
                                        <ul class="dropdown" style="display:none;">
                                            <li class="active" value="timer">
                                                Default Timer
                                            </li>
                                            <li value="stopwatch">
                                                Stopwatch
                                            </li>
                                        </ul>
                                    </span>
                                    <span class="tooltip timerScoreSettingsHelp" style="display:none;" title="Achievements and challenges cannot be earned when playing in Stopwatch mode.">
                                        <div class="help-icon" title="Timer Settings">
                                            
                                        </div>
                                    </span>
                                </span>
                                <span id="timeInnerBox">
                                    <span id="time">
                                        
                                    </span>
                                    <span id="giveUp"><a href="javascript:void(0);">Give Up</a></span>
                                </span>
                            </span>
                        </div>
                        <div id="playGameBar">
                            <div id="playGameBox" style="">
                                <div id="playPadding">
                                    <a class="button-wrapper" href="javascript:void(0);" id="play-quiz">
                                        <div class="game-button lrg" id="button-play">
                                            <span>
                                                Play Quiz
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <span id="answer-wrapper" style="display:none;">
                                    <span id="answerBox">
                                        <label id="answerText">
                                            Enter brand:
                                        </label>
                                        <input autocapitalize="off" autocomplete="off" autocorrect="off" class="answerEntry" id="gameinput" size="20" spellcheck="true" type="text" autofocus>
                                        </input>
                                    </span>
                                </span>
                            </div>
                            <div id="postGameBox" style="display:none;">
                                <div class="reckon-section" id="gameOverMsg">
                                    <div class="reckon-title">
                                        Your Score
                                    </div>
                                    <div class="reckon-score">
                                        <span id="userPct">
                                        </span>
                                    </div>
                                </div>
                                <div id="snark">
                                    
                                </div>
                                <div class="reckon-section" id="reckonMsg">
                                    <div class="reckon-title">
                                        Avg Score
                                    </div>
                                    <div class="reckon-score">
                                        <span id="avgPct">15%</span>
                                        
                                    </div>
                                </div>
                                <!--<div id="reckonStats" class="reckon-section">
										<div class="reckon-title">Quiz Stats</div>
							    		<a class="button-wrapper" href="/games/g/corplogos/results">
							    			<div id="button-stats" class="game-button sm"><span></span></div>
							    		</a>
						    		</div>-->
                            </div>
                        </div>
                    </div>
                    <?php if(isset($nextLinkRs) && !empty($nextLinkRs) && isset($randomLinkRs) && !empty($randomLinkRs)){ ?>
                    <?php $currentPageWpSlug = get_queried_object()->post_name; ?>
                    <div id="reckonBox" style="display:none;">
                        <?php if(!empty($nextLinkRs->qid) && $id != $nextLinkRs->qid){ ?>
                        <a class="button-wrapper tooltip delay" href="<?php echo !empty($nextLinkRs->slug) ? '/' . $currentPageWpSlug . '/' . $nextLinkRs->slug : '?qid='.$nextLinkRs->qid;  ?>" id="next-quiz-button-wrapper" title="Play next quiz: Corporate Logos II">
                            <div class="game-button sm" id="button-next-quiz" title="">
                                <span>
                                    Next Quiz
                                </span>
                            </div>
                        </a>
                        <?php } ?>
                        <div id="reckoning-playlist-container">
                        </div>
                        <a class="button-wrapper" href="<?php echo !empty($randomLinkRs->slug) ? '/' . $currentPageWpSlug . '/' . $randomLinkRs->slug : '?qid=' . $randomLinkRs->qid; ?>" id="rand-button-wrapper">
                            <div class="game-button sm" id="button-random">
                                <span>
                                    Play Another
                                </span>
                            </div>
                        </a>
                        <div class="clearfix">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="clearfix">
                    </div>
                    <div id="gameHeaderTransition" style="display:none;">
                    </div>
                </div>
                <div id="gridContainer">
                </div>
                <?php 
                $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                ?>
                
                <div id="Inline" class="st-button-preview-wrap selected-button"><h5>Share your score with friends:</h5><div id="st-inline-button-preview"><div id="st-el-1" class=" st-center st-has-labels  st-inline-share-buttons st-animated "><div class="st-btn st-first" data-network="facebook" style="display: inline-block;">
                <!-- <img src="https://platform-cdn.sharethis.com/img/facebook.svg"> -->
                <span class="st-label"><div class="fb-share-button" data-href="<?php echo $link; ?>" data-layout="button_count"></div></span>
                </div><div class="st-btn" data-network="twitter" style="display: inline-block;">
                <img src="https://platform-cdn.sharethis.com/img/twitter.svg">
                <span class="st-label">
                    <a class="twitter-share-button" href="https://twitter.com/intent/tweet?url=<?php echo $link; ?>&text=Hello%20world">Tweet</a>
                </span>
                </div><div class="st-btn" data-network="pinterest" style="display: inline-block;">
                <img src="https://platform-cdn.sharethis.com/img/pinterest.svg">
                <span class="st-label">Pin</span>
                </div></div></div></div>


            </div>
        </div>
    </div>
</div>
<?php 
$ip_address = $_SERVER['REMOTE_ADDR'];
//echo '<pre>';print_r($avgScores);exit;
foreach($answersResultSet as $k => $answer) {
	$answer->answer = base64_encode(ucwords($answer->answer));
	$answer->joined = base64_encode(str_replace(' ', '', ucwords($answer->answer)));
}
?>

<script type="text/babel">
    <?php if($answersResultSet) { ?>
			const answers = [
		<?php 	foreach($answersResultSet as $k => $answer) { ?>
					{name: "<?php echo ucwords($answer->answer) ?>", joined: "<?php echo str_replace(' ', '', ucwords($answer->answer)); ?>", img: "<?php echo $answer->image ?>"},
		<?php 	} ?>
			];
<?php } ?>

var base = new Base(answers,'<?php echo $resultSet->gameTime; ?>','<?php echo ucfirst($id); ?>','<?php echo ucfirst($quizTitle); ?>','<?php echo get_current_user_id(); ?>','<?php echo $ip_address; ?>','<?php echo $avgScores; ?>');
</script>