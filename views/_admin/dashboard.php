<?php if (isset($entries)): ?>
<?=js('flot', FORMS_FOLDER)?>

<script type="text/javascript" >

	jQuery(function($) {
		
		$('#entries').css({
			height: '300px',
			width: '95%'
		});

		var plotData = [
		<?php foreach($entries as $key => $val) : ?>
		{label: '<?=$key?>', data: <?=json_encode($val)?>}
		<?php if ($key != count($entries) - 1) : ?>, <?php endif; ?>
		<?php endforeach; ?>
		];
		$.plot($('#entries'), plotData, {
		//$.plot($('#entries'), [{ label: 'Visits', data: visits },{ label: 'Page views', data: views }], {
			lines: { show: true },
			points: { show: true },
			grid: { hoverable: true, backgroundColor: '#fffaff' },
			series: {
				lines: { show: true, lineWidth: 1 },
				shadowSize: 0
			},
			xaxis: { mode: "time" },
			yaxis: { min: 0},
			selection: { mode: "x" }
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
	 
		var previousPoint = null;
		
		$("#entries").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));
	 
				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						
						$("#tooltip").remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];
						
						showTooltip(item.pageX, item.pageY,
									item.series.label + " : " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;            
				}
		});
	
	});
</script>
<h2>Entries Traffic</h2>
<div id="entries" class="line" style="padding-bottom: 10px;"></div>
<?php endif; ?>