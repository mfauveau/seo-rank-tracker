<div id="history">

	<div id="history-graph"></div> 

	<?
	$table = new CI_Table;
	$table->set_template($cp_table_template);
	
	$table->set_heading("#", $this->lang->line('seo_rank_tracker_keywords'), $this->lang->line('seo_rank_tracker_rank'), $this->lang->line('seo_rank_tracker_search_engine'), $this->lang->line('seo_rank_tracker_date'));
	
	foreach($ranks as $r) {
		$table->add_row("-", $r->keywords, $r->rank, $r->search_engine_full, $r->date_full);
	}
	
	echo $table->generate();
	?>
</div>
 
    

<script id="source"> 
$(function () {

	/* donnes */
    var d = [[1196463600000, 0], [1196550000000, 0], [1196636400000, 0], [1196722800000, 77]];
    
    <?
    $str = "";
    foreach($ranks as $r) {
    	if($r->rank == 0) {
    		$str .= "[".$r->date."000, -100], ";
    	} else {
    		$str .= "[".$r->date."000, -".$r->rank."], ";
    	}
    	
    }
    
    $str = substr($str, 0, -2);
    echo "var d = [".$str."]";
    ?>
 
    // first correct the timestamps - they are recorded as the daily
    // midnights in UTC+0100, but Flot always displays dates in UTC
    // so we have to add one hour to hit the midnights in the plot
    for (var i = 0; i < d.length; ++i)
      d[i][0] += 60 * 60 * 1000;
 
 
    // helper for returning the weekends in a period
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);
 
        return markings;
    }
    
    var options = {
        yaxis : {max: -1, min:-100 },
        xaxis: { mode: "time", timeformat:"%y/%m/%d"},
        grid: { markings: weekendAreas, hoverable:true },
        series: {
        	lines: {show:true},
        	points: {show:true, radius:4}
        }
    };
    
    var plot = $.plot($("#history-graph"), [d], options);

	$("#history-graph").bind("plothover", function (event, pos, item) {

        if (item) {
        	$("#tooltip").remove();
          	showTooltip(item.pageX, item.pageY, "Rank : "+item.datapoint[1]);
        } else {
        	$("#tooltip").remove();
        }
    });

	function showTooltip(x, y, contents) {
		
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

    
});
</script> 