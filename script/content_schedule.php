<?php

	// CROPS
	$result = SqlQuery("SELECT * FROM `${g_SQLTablePrefix}crop_event`");
	$g_CropEvents = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($g_CropEvents);
	
	$result = SqlQuery("SELECT `id`,`name` FROM `${g_SQLTablePrefix}crop`");
	$fetchedResult = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($fetchedResult);
	$g_CropNames = array();
	foreach($fetchedResult as $entry)
		$g_CropNames[$entry['id']] = $entry['name'];
	//var_dump($g_CropNames);

	// VARIETIES
	$result = SqlQuery("SELECT * FROM `${g_SQLTablePrefix}variety_period`");
	$g_VarietyPeriods = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($g_CropEvents);

	$result = SqlQuery("SELECT `id`,`name` FROM `${g_SQLTablePrefix}variety`");
	$fetchedResult = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($fetchedResult);
	$g_VarietyNames = array();
	foreach($fetchedResult as $entry)
		$g_VarietyNames[$entry['id']] = $entry['name'];
	//var_dump($g_VarietyNames);

	
	/**  */
	function DisplayMonth($month, $year, $hide = FALSE)
	{
		global $g_CropEvents, $g_CropNames, $g_VarietyPeriods, $g_VarietyNames;
		
		$html  = "<div class='month' id='month_$month'>\n";
		$html .= "<div class='monthname' >". ucfirst(Loc("date.month.$month")) ." $year</div>\n";
		$style = $hide ? "display:none;" : "";
		$html .= "<section class='monthdiv' style='$style'>\n";

		$firstTime = mktime(0, 0, 0, $month, 1, $year);
		$first = getdate($firstTime);
		$lastDay = date("t", $firstTime);
		$lastTime = mktime(0, 0, 0, $month, $lastDay, $year);
		$last = getdate($lastTime);
		
		AddLog("First day num: ". $first["weekday"]);

		for($i=0; $i<7; $i++)
		{
			$html .= "<div class='weekday'>". ucfirst(Loc("date.day.$i")) ."</div>\n";
		}

		$day = 1;
		for($i=0; $i<43; $i++)
		{
			if($i < $first["wday"])
				$html .= "<div class='day empty'></div>\n";
			else if($day <= $lastDay)
			{
				$class = (date("Y-n-j") == "$year-$month-$day") ? "today " : "";
				$class .= ((($i / 7) % 2) == 0) ? "even" : "odd";
				//$class .= ((($i % 7) % 2) == 0) ? "even" : "odd";
				$html .= "<div class='day $class'>";
				$html .= "<div class='daynum'>$day</div>";
				
				// Crops
				foreach($g_CropEvents as $event)
				{
					if((substr($event['date'], 0, 10) == sprintf("%04d-%02d-%02d", $year, $month, $day)) && isset($g_CropNames[$event['crop']]))
						$html .= "<div style='width:100%;'><img src='pub/img/vegetables/seedling_1f331.png' style='width:14px; margin-right:4px;'>". Loc("crop_event.type.".$event['type']) .": ". $g_CropNames[$event['crop']] ."</div>";
				}

				// Variety
				$periodCount = 0;
				$date = mktime($year, $month, $day);
				foreach($g_VarietyPeriods as $event)
				{
					list($startYear, $startMonth, $startDay) = sscanf($event['date_start'], "%04d-%02d-%02d");
					$startDate = mktime($year, $startMonth, $startDay);
					$isFirst = (($month == $startMonth) && ($day == $startDay));

					list($endYear, $endMonth, $endDay) = sscanf($event['date_end'], "%04d-%02d-%02d");
					$endDate = mktime($year, $endMonth, $endDay);
					$isLast = (($month == $endMonth) && ($day == $endDay));
					
					$inRange = ($endDate > $startDate) ? (($date >= $startDate) && ($date <= $endDate)) : (($date <= $endDate) || ($date >= $startDate));
					if($inRange && isset($g_VarietyNames[$event['variety']]))
					{				
						$periodClass = "period";
						if($isFirst)
							$periodClass .= " first";
						else if($isLast)
							$periodClass .= " last";
						$periodClass .= ($periodCount % 2 == 0) ? " even" : " odd";
						$html .= "<div class='$periodClass' style='background:". $event['color'] .";' >";
						if($isFirst)
							$html .= "<img src='pub/img/interface/ledger_1f4d2.png' style='width:14px; margin-right:4px;' />";
						$html .= Loc("variety_period.type.".$event['type']) .": ". $g_VarietyNames[$event['variety']] ."</div>";
						$periodCount++;
					}
				}
				
				$html .= "</div>\n";
				$day++;
			}
			else if(($i - ($lastDay + $first["wday"]) + $last["wday"] + 1) < 7)
				$html .= "<div class='day empty'></div>\n";
		}
	  
		$html .= "</section>\n";
		$html .= "</div>\n";
		return $html;
	}


	$time = getdate();
	//var_dump($time);
	
	$year = $time["year"];
	$month = $time["mon"];
	$day = $time["mday"];
	$weekday = $time["weekday"];
	$lastday = date("t");

	$html = "";
	for($i = 1; $i <= 12; $i++)
	{
		$html .= DisplayMonth($i, $year/*, $i != $month*/);
	}
	
	print($html);

?>
