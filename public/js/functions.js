
function waiting_cursor(text, count) {
    if (count > 1000000) {
		count = 0;
    }
	(count % 2 == 0) ? under_score = "_" : under_score = " ";

	document.getElementsByTagName("H1")[0].innerHTML = text+under_score;
	
	var t = setTimeout(function() {
	    	waiting_cursor(text, count+1)
		}, 1500);
}

// $(function () {
// 	$("nav img")
// 		.mouseover( function() {
// 			// var src = $(this).attr("src").match(/[^\.]+/)+"_color.png";
// 			// $(this).attr("src", src);
// 		})
// 		.mouseout( function() {
// 			var src = $(this).attr("src").replace("_color.png", ".png");
// 			$(this).attr("src", src);
// 		});
// });

// Trigger underscore flash
//$(document).ready(function() {
	//waiting_cursor(document.getElementsByTagName("H1")[0].innerHTML, 0);
//});