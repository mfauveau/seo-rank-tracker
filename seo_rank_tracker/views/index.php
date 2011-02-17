<div id="rank-tracker">
<?
echo form_open($form_action);

?>
<h1><?=$this->lang->line('seo_rank_tracker_follow_new_rank')?></h1>
	<div id="new-rank">
		<p>
			<div id="new-rank-left">
			<?
		
			$options = array(
				'name' => "q",
				'value' => $this->input->post('q'),
				'size' => 50
			);
			echo form_input($options, '', 'style="width:500px;"');
			?>
			</div>
			<div id="new-rank-right">
				<?
				$default_val = get_cookie('search_engine_'.$site_id);
				if($this->input->post('search_engine')) {
					$default_val = $this->input->post('search_engine');
				}
				echo form_dropdown('search_engine', $search_engines_dropdown, $default_val);
				?>
				&nbsp;
				<input type="submit" class="submit" value="<?=$this->lang->line('seo_rank_tracker_follow_this_expression')?>" />
			</div>

		</p>
	</div>
<?
echo form_close();
?>



<?

if($ranks) {
	?>
	
	<h1><?=$this->lang->line('seo_rank_tracker_followed_ranks')?> <a  id="refresh-ranks" href="#"><?=$this->lang->line('seo_rank_tracker_refresh_now')?></a></h1>
	<div id="ranks">
	<?
	$table = new CI_Table;
	$table->set_template($cp_table_template);
	$table->set_heading("#", $this->lang->line('seo_rank_tracker_keywords'), $this->lang->line('seo_rank_tracker_rank'), $this->lang->line('seo_rank_tracker_search_engine'), $this->lang->line('seo_rank_tracker_date'), $this->lang->line('seo_rank_tracker_delete'));
	
	$i=1;
	foreach($ranks as $r) {
		if($r->rank == 0) {
			$table->add_row('<span class="nb">'.$i.'</span>', $r->history_link, '<span class="rank">'."Not in top 100".'</span>', $search_engines[$r->search_engine][0], '<span class="date">'.strftime("%Y-%m-%d à  %H:%M:%S", $r->date).'</span>', $r->delete_link);
		} else {
			$table->add_row('<span class="nb">'.$i.'</span>', $r->history_link, '<span class="rank">'.$r->rank.'</span>', $search_engines[$r->search_engine][0], '<span class="date">'.strftime("%Y-%m-%d à  %H:%M:%S", $r->date).'</span>', $r->delete_link);
		}
/*
		if($r->rank == 0) {
			$table = myAddRow($table, $i, '<a href="'.$r->details_url.'">'.$r->keywords.'</a>', "Not in top 100", strftime("%d/%m/%Y %H:%M:%S",$r->date), '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo'.AMP.'method=rank_tracker_ajax'.AMP.'rank_id='.$r->id.'" class="refresh-rank">Refresh Now</a>');
		} else {
			$table = myAddRow($table, $i, '<a href="'.$r->details_url.'">'.$r->keywords.'</a>', $r->rank, strftime("%d/%m/%Y %H:%M:%S",$r->date), '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo'.AMP.'method=rank_tracker_ajax'.AMP.'rank_id='.$r->id.'" class="refresh-rank">Refresh Now</a>');
		}
*/
		$i++;
	}
	
	echo $table->generate();
	?>
	</div>
	<?
}

function myAddRow($table, $c1, $c2, $c3, $c4, $c5) {
	$id = $c1;
	$c1 = '<span class="rank-id" rel="'.$id.'">'.$c1.'</span>';
	$c2 = '<span class="rank-keywords" rel="'.$id.'">'.$c2.'</span>';
	$c3 = '<span class="rank-rank" rel="'.$id.'">'.$c3.'</span>';
	$c4 = '<span class="rank-date" rel="'.$id.'">'.$c4.'</span>';
	$c5 = '<span class="rank-refresh" rel="'.$id.'">'.$c5.'</span>';
	$table->add_row($c1, $c2, $c3, $c4, $c5);
	return $table;
}

?>

<p id="cron-link"><span><?=$this->lang->line('seo_rank_tracker_url_for_autoupdate')?> : <a href="<?=$cron_url?>"><?=$cron_url?></a></span></p>

</div>