<?php
	
function time2str($ts)
{
    if(!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'Just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'Last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
}

function formatDate($timestamp = null,$q,$default = 'j M Y'){
	
	if(!$timestamp) return null;
	
	if($q['datedisplay']){
		if($q['datedisplay'] == 'relative'){
			$date = time2str($timestamp);
		} else {
			$date = date($q['datedisplay'],$timestamp);
		}
	} else {
		$date = date($default,$timestamp);
	}
	
	return $date ? $date : null;
}

function similarByString($string,$matchfield,$returnfields = null,$type = null){
	
	//if($string == '' || $matchfield == '' || $returnfields == '' || $type == '') return false;
	
	global $wpdb;
	$keywords = array_filter(explode(' ', $string));
	
	
/*
	$kwreg = implode('|', $keywords);
	$keymatch = $wpdb->get_col("select post_id from $wpdb->postmeta where ".$matchfield." REGEXP '".$kwreg."' ");
*/

	$keymatches = array();
	foreach($keywords as $word){
		$thismatch = $wpdb->get_col("SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '$matchfield' AND (meta_value LIKE '{$word}%' OR meta_value LIKE '%{$word}') ");
		
		$keymatches = array_unique(array_merge($keymatches,$thismatch));
		$keymatches = count($keymatches) > 0 ? $keymatches : array(0);
	}

	// $args = array(	'post_type' => $type,
	// 				'posts_per_page' => -1,
	// 				'post__in' => $keymatches
	// );
	
	// $titlematches = new WP_Query($args);
	
	// while($titlematches->have_posts()) : $titlematches->the_post();
	
	// 	$item['ID'] = get_the_ID();
	// 	$item[$matchfield] = get_post_meta(get_the_ID(),$matchfield,true);
	// 	foreach($returnfields as $field){
	// 		$item[$field] = get_post_meta(get_the_ID(),$field,true);		
	// 	}
		
	// 	$item_keywords = array_filter(explode(' ', $item[$matchfield]));
	// 	$item['matchcount'] = count(array_intersect($keywords, $item_keywords));
		
	// 	$items[] = $item;
		
	// endwhile; wp_reset_postdata();
	
	// $result = sort2d($items, 'matchcount','DESC',false);
	return $keymatches;

	
}

function sort2d ($array, $index, $order='ASC', $natsort=TRUE, $case_sensitive=FALSE){
    if(is_array($array) && count($array)>0){
        foreach(array_keys($array) as $key)
            $temp[$key]=$array[$key][$index];
            if(!$natsort)($order=='ASC')? asort($temp) : arsort($temp);
            else {
                ($case_sensitive)? natsort($temp) : natcasesort($temp);
                if($order!='ASC') $temp=array_reverse($temp,TRUE);
            }
        foreach(array_keys($temp) as $key) (is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
        return $sorted;
    }
    return $array;
}