<div id="logo" style="font-size:500%; font-weight:bold; text-align:center;">畑</div>
<div style="width:100%; display:flex; flex-flow:row; border-bottom: solid black thin;">
	<div id="date" style="" ><?php print ucfirst(strftime(Loc("date.format.full"))); ?></div>
	<div id="moon" style="flex-grow:1;" >
		<?php 
			$moonInfo = array(
				'New Moon'			=> 'pub/img/moon/new-moon-symbol_1f311.png',
				'Waxing Crescent'	=> 'pub/img/moon/waxing-crescent-moon-symbol_1f312.png',
				'First Quarter'		=> 'pub/img/moon/first-quarter-moon-symbol_1f313.png',
				'Waxing Gibbous'	=> 'pub/img/moon/waxing-gibbous-moon-symbol_1f314.png',
				'Full Moon'			=> 'pub/img/moon/full-moon-symbol_1f315.png',
				'Waning Gibbous'	=> 'pub/img/moon/waning-gibbous-moon-symbol_1f316.png',
				'Third Quarter'		=> 'pub/img/moon/last-quarter-moon-symbol_1f317.png',
				'Waning Crescent'	=> 'pub/img/moon/waning-crescent-moon-symbol_1f318.png',
			);
			$moon = new Solaris\MoonPhase();
			$moonPhase = $moon->phase_name();
			$moonImg = $moonInfo[$moonPhase];
			print "<img src='$moonImg' style='height:16px; float:right;' alt='Moon phase is $moonPhase' title='$moonPhase' />\n";
		?>
	</div>
</div>