/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Polls 2.12										|
|	Copyright (c) 2006 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Polls Javascript File															|
|	- wp-content/plugins/polls/polls-js.js										|
|																							|
+----------------------------------------------------------------+
*/


// Variables
var polls = new sack(ajax_url);
var poll_id = 0;
var poll_answer_id = 0;
var poll_fadein_opacity = 0;
var poll_fadeout_opacity = 100;
var is_ie = (document.all && document.getElementById);
var is_moz = (!document.all && document.getElementById);
var is_opera = (navigator.userAgent.indexOf("Opera") > -1);
var is_being_voted = false;


// When User Vote For Poll
function poll_vote(current_poll_id) {
	if(!is_being_voted) {
		is_being_voted = true;
		poll_id = current_poll_id;
		poll_form = document.getElementById('polls_form_' + poll_id);
		poll_answer = eval("poll_form.poll_" + poll_id);
		poll_answer_id = 0;
		for(i = 0; i < poll_answer.length; i++) {
			if (poll_answer[i].checked) {
				poll_answer_id = poll_answer[i].value;
			}
		}
		if(poll_answer_id > 0) {
			poll_loading_text();
			poll_process();
		} else {
			alert("Please choose a valid poll answer.");
		}
	} else {
		alert("Your last request is still being processed. Please wait a while ...");
	}
}


// When User View Poll's Result
function poll_result(current_poll_id) {
	if(!is_being_voted) {
		is_being_voted = true;
		poll_id = current_poll_id;
		poll_loading_text();
		poll_process_result();
	} else {
		alert("Your last request is still being processed. Please wait a while ...");
	}
}


// When User View Poll's Voting Booth
function poll_booth(current_poll_id) {
	if(!is_being_voted) {
		is_being_voted = true;
		poll_id = current_poll_id;
		poll_loading_text();
		poll_process_booth();
	} else {
		alert("Your last request is still being processed. Please wait a while ...");
	}
}


// Poll Fade In Text
function poll_fadein_text() {
	if(poll_fadein_opacity == 90) {
		poll_unloading_text();
	}
	if(poll_fadein_opacity < 100) {
		poll_fadein_opacity += 10;
		if(is_opera) {
			poll_fadein_opacity = 100;
			poll_unloading_text();
		} else if(is_ie) {
			document.getElementById('polls-' + poll_id + '-ans').filters.alpha.opacity = poll_fadein_opacity;
		} else	 if(is_moz) {
			document.getElementById('polls-' + poll_id + '-ans').style.MozOpacity = (poll_fadein_opacity/100);
		}
		setTimeout("poll_fadein_text()", 100); 
	} else {
		poll_fadein_opacity = 100;
		is_being_voted = false;
	}
}


// Poll Loading Text
function poll_loading_text() {
	document.getElementById('polls-' + poll_id + '-loading').style.display = 'block';
}


// Poll Finish Loading Text
function poll_unloading_text() {
	document.getElementById('polls-' + poll_id + '-loading').style.display = 'none';
}


// Process The Poll
function poll_process() {
	if(poll_fadeout_opacity > 0) {
		poll_fadeout_opacity -= 10;
		if(is_opera) {
			poll_fadeout_opacity = 0;
		} else if(is_ie) {
			document.getElementById('polls-' + poll_id + '-ans').filters.alpha.opacity = poll_fadeout_opacity;
		} else if(is_moz) {
			document.getElementById('polls-' + poll_id + '-ans').style.MozOpacity = (poll_fadeout_opacity/100);
		}
		setTimeout("poll_process()", 100); 
	} else {
		poll_fadeout_opacity = 0;		
		polls.setVar("vote", true);
		polls.setVar("poll_id", poll_id);
		polls.setVar("poll_" + poll_id, poll_answer_id);
		polls.method = 'POST';
		polls.element = 'polls-' + poll_id + '-ans';
		polls.onCompletion = poll_fadein_text;
		polls.runAJAX();
		poll_fadein_opacity = 0;
		poll_fadeout_opacity = 100;
	}
}


// Process Poll's Result
function poll_process_result() {
	if(poll_fadeout_opacity > 0) {
		poll_fadeout_opacity -= 10;
		if(is_opera) {
			poll_fadeout_opacity = 0;
		} else if(is_ie) {
			document.getElementById('polls-' + poll_id + '-ans').filters.alpha.opacity = poll_fadeout_opacity;
		} else if(is_moz) {
			document.getElementById('polls-' + poll_id + '-ans').style.MozOpacity = (poll_fadeout_opacity/100);
		}
		setTimeout("poll_process_result()", 100); 
	} else {
		poll_fadeout_opacity = 0;
		polls.setVar("pollresult", poll_id);
		polls.method = 'GET';
		polls.element = 'polls-' + poll_id + '-ans';
		polls.onCompletion = poll_fadein_text;
		polls.runAJAX();
		poll_fadein_opacity = 0;
		poll_fadeout_opacity = 100;
	}
}


// Process Poll's Voting Booth
function poll_process_booth() {
	if(poll_fadeout_opacity > 0) {
		poll_fadeout_opacity -= 10;
		if(is_opera) {
			poll_fadeout_opacity = 0;
		} else if(is_ie) {
			document.getElementById('polls-' + poll_id + '-ans').filters.alpha.opacity = poll_fadeout_opacity;
		} else if(is_moz) {
			document.getElementById('polls-' + poll_id + '-ans').style.MozOpacity = (poll_fadeout_opacity/100);
		}
		setTimeout("poll_process_booth()", 100); 
	} else {
		poll_fadeout_opacity = 0;
		polls.setVar("pollbooth", poll_id);
		polls.method = 'GET';
		polls.element = 'polls-' + poll_id + '-ans';
		polls.onCompletion = poll_fadein_text;
		polls.runAJAX();
		poll_fadein_opacity = 0;
		poll_fadeout_opacity = 100;
	}
}