<?php
if(empty($_GET['file']) || !preg_match('/^([0-9A-Za-z]+)$/',$_GET['file'])){
	die('file not found');
} else {
  header("content-type: text/calendar");
	$file = './timetables/'.$_GET['file'];
}
$handle = fopen($file, "r");

#if(time() < strtotime("2nd July 2009")){
#	$first_date = strtotime('2008-10-6');
#} else if(time() < strtotime("2nd July 2010")){
#	$first_date = strtotime('2009-10-05');
#}

function get_firstmonday($month,$year){
	$num = date("w",mktime(0,0,0,$month,1,$year));
	if($num == 1)
		return mktime(0,0,0,$month,1,$year);
	elseif( $num > 1 )
		return mktime(0,0,0,$month,1,$year)+(86400*(8-$num));
	else
		return mktime(0,0,0,$month,1,$year)+(86400*(1-$num));
}

if(time() < strtotime("2nd July ".date("Y"))){
	$first_date = get_firstmonday('10', date("Y") - 1);
} else {
	$first_date = get_firstmonday('10', date("Y"));
}

$events = array();

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	if(!empty($data[0])){
		$events[] = data_to_ics($data, $first_date);
	}
}

function data_to_assoc($data){
	return array(
		'day'			=> $data[0],
		'start'			=> $data[1],
		'end'			=> $data[2],
		'summary'		=> $data[3],
		'description'	=> $data[4],
		'location'		=> $data[5],
		'weeks'			=> $data[6]
	);
}
function data_to_ics($data, $first_date){
	$data = data_to_assoc($data);
	$offset = array('Monday'=>0, 'Tuesday'=>1, 'Wednesday'=>2, 'Thursday'=>3, 'Friday'=>4);
	$out = array();
	$date = strtotime("+".$offset[$data['day']]." days", $first_date);
	$date_formated = date('Ymd', $date);
	$out['DTSTART'] = $date_formated.'T'.str_replace(':','',$data['start']).'00';
	$out['DTEND'] = $date_formated.'T'.str_replace(':','',$data['end']).'00';
	$out['SUMMARY'] = trim($data['summary']);
	$out['DESCRIPTION'] = trim($data['description']);
	$out['LOCATION'] = trim($data['location']);
	$out['EXDATE'] = '';
	$weeks_on = get_weeks_on($data['weeks']);
	$execptions = get_exceptions($weeks_on, $date, $data['start']); 
	foreach($execptions as $e){
		$out['EXDATE'] .= "EXDATE:".$e."\n";
	}
	$out['COUNT'] = max($weeks_on);
	return $out;
	
}
function get_weeks_on($weeks){
	$weeks = explode(',', $weeks);
	$weeks_on = array();
	foreach($weeks as $week){
		$week_numbers = explode('-', trim($week));
		if(count($week_numbers) == 1){
			$weeks_on[] = $week_numbers[0];
		} else {
			for($i = $week_numbers[0]; $i <= $week_numbers[1]; $i++){
				$weeks_on[] = $i;
			}
		}
	}
	return $weeks_on;
}
function get_exceptions($weeks_on, $start_date, $start_time){
	$max = max($weeks_on);
	$exception = array();
	for($i = 1; $i <= $max; $i++){
		if(!in_array($i, $weeks_on)){
			$exception[] = date('Ymd', strtotime('+'.($i-1).' week', $start_date)).'T'.str_replace(':','',$start_time).'00';;
		}
	}
	return $exception;
}


fclose($handle);

?>BEGIN:VCALENDAR
PRODID:-//E26//TEST//EN
VERSION:2.0

<?php

foreach($events as $e){
	?>
BEGIN:VEVENT
DTSTART:<? print $e['DTSTART']; ?>

DTEND:<?=$e['DTEND']?>

RRULE:FREQ=WEEKLY;INTERVAL=1;COUNT=<?=$e['COUNT']?>

SUMMARY:<?=$e['SUMMARY']?>

LOCATION:<?=$e['LOCATION']?>

DESCRIPTION:<?=$e['DESCRIPTION']?>

<?=$e['EXDATE']?>END:VEVENT


<? } ?>
END:VCALENDAR
