/**
 * Principal class. Will be passed as argument to others.
 * @class Base
 */
let Base = class {
	/**
	 * @constructor
	 */
	 constructor(arr,gameTime,quizId,quizTitle,winnerId,ipAddress,avg) {
		this.answers = arr;
		this.questions = arr.length;
		this.score = 0;
		this.avg = avg;
		this.ip = ipAddress;
		this.percentage = 0;
		this.isGiveup = 0;
		this.quizId = quizId;
		this.quizTitle = quizTitle;
		this.winnerId = winnerId;
		this.interval;
		this.gameTime = gameTime;
		this.countDownDate;
		this.createContainer();
		document.getElementById("time").innerHTML = '0' + this.gameTime + ":" + '00';
		document.getElementById('play-quiz').addEventListener('click', this.play, false);
	 }
	 
	 play = () => {
		this.countDownDate = new Date(new Date().getTime() + 1 * 60000);
		this.countDownDate.setSeconds(this.countDownDate.getSeconds() + 61);
		document.getElementById('playPadding').style.display = 'none';
		document.getElementById('answer-wrapper').style.display = 'inline-block';
		document.getElementById('giveUp').style.display = 'block';
		this.startTimer();
		this.compare();
	 }
	 
	 startTimer = () => {
		this.interval = setInterval(this.newTimer, 1000);
	 }
	 
	 newTimer = () => {
		let now = new Date().getTime();
		  let distance = this.countDownDate - now;
		  let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  let seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  seconds = seconds < 10 ? '0'+seconds : seconds;
		  if(minutes >= 0 || seconds >= 0){
			document.getElementById("time").innerHTML = '0'+minutes + ":" + seconds;
		  }
		  if (distance < 0) {
			this.percentage = Math.round((this.score*100)/this.questions);
			this.stopTimer();
			this.stopPlay();
		  }
	 }
	 
	 stopTimer = () => {
		clearInterval(this.interval);
	 }
	 
	 compare = () => {
		let gameinput = document.getElementById('gameinput');
		let giveup = document.getElementById('giveUp');

		giveup.addEventListener("click", event => {
			if (confirm("Are you sure you want to giveup?")){
				this.percentage = Math.round((this.score * 100) / this.questions);
				this.isGiveup = 1;
				this.stopTimer();
				this.stopPlay();
			}
			
		});

		gameinput.addEventListener("keydown", event => {
			let charCode = (event.which) ? event.which : event.keyCode;
			if ((charCode >= 65 && charCode < 91) || (charCode >= 97 && charCode < 123) || charCode == 32 || charCode == 8) {

			} else {
				event.preventDefault();
			}
		});

		gameinput.addEventListener("keyup", event => {
		    if (event.isComposing || event.keyCode === 229) {
			 return;
		    }
		    let charCode = (event.which) ? event.which : event.keyCode;
			if ((charCode >= 65 && charCode < 91) || (charCode >= 97 && charCode < 123) || charCode == 32 || charCode == 8) {
				let string = this.toUpperCase(gameinput.value);
				let flag = this.answers.findIndex(k => k.name == btoa(string)) > -1 ? this.answers.findIndex(k => k.name == btoa(string)) : this.answers.findIndex(k => k.joined == btoa(string));
				if (flag > -1) {
					this.score++;
					document.getElementById('currentScore').innerHTML = this.score + '/' + this.questions;
					let slot = document.getElementById('slot' + flag);
					slot.innerHTML = atob(this.answers[flag].name);
					slot.style.background = '#6BFF33';
					$("#slot"+flag).animate({backgroundColor: '#ffffff'});
					let timestamp = new Date().getTime();
					this.answers[flag].name = btoa(timestamp);
					this.answers[flag].joined = btoa(timestamp);
					document.getElementById('currentScore').innerHTML = this.score + '/' + this.questions;
					gameinput.value = '';
					if (this.score == this.questions) {
						this.percentage = Math.round((this.score * 100) / this.questions);
						this.stopTimer();
						this.stopPlay();
					}
				}
			}
		  
		});
	 }
	 
	 toUpperCase = (str) => {
		var splitStr = str.toLowerCase().split(' ');
	    for (var i = 0; i < splitStr.length; i++) {
		   splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
	    }
	    return splitStr.join(' '); 
	 }
	 
	 createContainer() {
		{
		    document.getElementById('currentScore').innerHTML = this.score+'/'+this.answers.length;
			let container = document.createElement('div');
			container.id = 'gameTable';
			let count = -1;
			var totalRows = Math.ceil(this.answers.length);
			for (var i = 0; i < totalRows; i++) {
				count++;
				let cell = document.createElement('div');
				let bgImage = `'${this.answers[count].img}'`;
				cell.innerHTML = '<div class="char_box" id="box'+count+'"><div class="d_extra" id="name'+count+'" style="background-image: url('+bgImage+'); width: 94px; background-position: center; background-size: cover;"></div><div class="d_value ui-widget-content ui-corner-all" style="color:#000;background:#fff;" id="slot'+count+'"></div></div>';
				
				container.appendChild(cell);
			}
			document.getElementById('gridContainer').appendChild(container);
		}
	 }
	 
	 stopPlay = () => {
		document.getElementById('answer-wrapper').style.display = 'none';
		document.getElementById('reckonBox').style.display = 'block';
		document.getElementById('postGameBox').style.display = 'block';
		document.getElementById('userPct').innerHTML = this.percentage+'%';
		document.getElementById('giveUp').style.display = 'none';
		
		var message;
		switch(true) {
		  case (this.percentage == 100):
			message = 'Manx as the hills';
			break;
		  case (this.percentage >= 90 && this.percentage <= 99):
			message = 'You got an A+';
			break;
		  case (this.percentage >= 80 && this.percentage <= 89):
			message = 'You got an A-';
			break;
		  case (this.percentage >= 70 && this.percentage <= 79):
			message = 'You got an B+';
			break;
		  case (this.percentage >= 60 && this.percentage <= 69):
			message = 'You got an B-';
			break;
		  case (this.percentage >= 50 && this.percentage <= 59):
			message = 'You got an C+';
			break;
		  case (this.percentage >= 40 && this.percentage <= 49):
			message = 'You got an C-';
			break;
		  case (this.percentage >= 30 && this.percentage <= 39):
			message = 'You got an D+';
			break;
		  case (this.percentage >= 20 && this.percentage <= 29):
			message = 'You got an D-';
			break;
		  case (this.percentage > 0 && this.percentage <= 19):
			message = 'You got an E!';
			break;
		   case (this.percentage == 0):
			message = 'Poor poor, try again';
			break;
		}

		document.getElementById('snark').innerHTML = message;
		document.getElementById('avgPct').innerHTML = Math.round(this.avg)+'%';
		
		// This does the ajax request
		$.ajax({
			url: 'http://165.22.115.115/wp-admin/admin-ajax.php', // or example_ajax_obj.ajaxurl if using on frontend
			type: 'POST',
			data: {
				'action': 'record_score',
				'qid': this.quizId,
				'title': this.quizTitle,
				'score': this.score,
				'percentage': this.percentage,
				'time': this.gameTime,
				'isGiveup': this.isGiveup,
				'winnerId': this.winnerId,
				'ip': this.ip
			},
			success: function (data) {
				// This outputs the result of the ajax request
				console.log(data);
			},
			error: function (errorThrown) {
				console.log(errorThrown);
			}
		});
		 
	 }
	 
}
