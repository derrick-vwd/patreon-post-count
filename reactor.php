<?php
	$publisher = $argv[1];
	$link = 'https://patreon.com/' . $publisher;
	$html_file = file_get_contents($link);

	$post_count_position = strpos($html_file, 'creation-count');
	// 'creation-count' is the data-tag attribute in the span class where posts are located.
	$post_count = substr($html_file, $post_count_position, 65);
	$post_count = preg_replace('/[^0-9]/', '', $post_count);
	// Anything that is not a number will be replaced with an empty string.
	
	$report = $argv[1] . ', of Patreon, has ' . $post_count . ' posts today.\n'; 

	function savePostCount($publisher, $count)
	{
		// Look in file and check if the publisher exists.
		$file_does_exist = file_exists('./publisher-post-history.txt');
		if($file_does_exist)
		{
			$publisher_file = file_get_contents('./publisher-post-history.txt');
			$publisher_arr = explode(" ", $publisher_file);
			$publisher_exists = array_key_exists($publisher, $publisher_arr);
			if($publisher_exists)
			{
				echo "Publisher found.";
				$publisher_position = array_search($publisher, $publisher_arr);
				$publisher_old_post_count = $publisher_arr[$publisher_position+1];
				$publisher_uploads_since_last_check = $count-$publisher_uploads_since_last_check;
				if($publisher_uploads_since_last_check < 0)
				{
					echo "Publisher removed " . abs($publisher_uploads_since_last_check) . " posts."; 
				}
				else
				{
					echo "Publisher has added " . $publisher_uploads_since_last_check . " more posts."
				}
			}
		}
		else
		{
			echo 'File does not exist';
		}
	}

	savePostCount($publisher, $post_count);
