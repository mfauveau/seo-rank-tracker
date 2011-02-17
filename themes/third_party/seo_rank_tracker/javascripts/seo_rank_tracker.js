$(document).ready(function() {
	var in_progress = false;

  $('#refresh-ranks').click(function() {
  
  	if(in_progress == false) {
  	in_progress = true;
	
	var refreshBtn = $(this);
	
	refreshBtn.addClass('active');
	
	var bt_ranks_to_refresh = $("#ranks table tr .refresh").length;
	
	$("#ranks table tr .refresh").each(function() {
		var parent = $(this).parent().parent();
		var previousBackground = parent.find('td').css('background');
		//parent.find('td').css('background', '#C8D1D7');
		parent.find('.nb').addClass('loading');
		
		var ajaxLink = $(this).attr('href');
		

		$.ajax({
		   type: "POST",
		   url: ajaxLink,
		   dataType:"json",
		   success: function(msg){

		   		parent.find('td .rank').text(msg.rank);
		   		parent.find('td .date').text(msg.date);
		     	parent.find('td').css('background', previousBackground);
		     	bt_ranks_to_refresh = bt_ranks_to_refresh - 1;
		     	
				parent.find('.nb').removeClass('loading');
		     	
		     	if(bt_ranks_to_refresh == 0) {
					refreshBtn.removeClass('active');
					in_progress = false;
		     	}

		     	
		   }
		 });
		
	});
	
	}

  	return false;
  });
});