<?php
	$publisher = strtolower($argv[1]);
	$link = 'https://patreon.com/' . $publisher;
	$html_file = file_get_contents($link);

	$post_count_position = strpos($html_file, 'creation-count');
	// 'creation-count' is the data-tag attribute in the span class where posts are located.
	$post_count = substr($html_file, $post_count_position, 65);
	$post_count = preg_replace('/[^0-9]/', '', $post_count);
	// Anything that is not a number will be replaced with an empty string.

	checkPostCount($publisher, $post_count);

	function savePostCount($publisher, $count, $msg, $first=1)
	{
		$fh = fopen('./publisher-post-history.txt', 'a+');
		if(!$first)
		{
			$record = " ";
		}
		else
		{
			$record = "";
		}
		$record .= $publisher . ' ' . $count;
		fwrite($fh, $record);
		echo $msg;
	}
	
	function updatePostCount($publisher, $count, $arr)
	{
		echo "Publisher found.\n";
		$publisher_position = array_search($publisher, $arr);
		$count_position = ++$publisher_position;
		$publisher_old_post_count = $arr[$count_position];
		$publisher_uploads_since_last_check = $count-$publisher_old_post_count;
		if($publisher_uploads_since_last_check < 0)
		{
			echo "Publisher removed " . abs($publisher_uploads_since_last_check) . " posts.\n"; 
		}
		else
		{
			echo "Publisher has added " . $publisher_uploads_since_last_check . " more posts.\n";
		}
		// Everything above here is correct.
		$arr[$count_position] = $count;

		$publisher_file = implode(" ", $arr);

		file_put_contents('./publisher-post-history.txt', $publisher_file);
		echo "Updated publisher post count.\n";
	}

	function checkPostCount($publisher, $count)
	{
		// Look in file and check if the publisher exists.
		$file_does_exist = file_exists('./publisher-post-history.txt');
		if($file_does_exist)
		{
			$publisher_file = file_get_contents('./publisher-post-history.txt');
			
			$publisher_arr = explode(" ", $publisher_file);

			$publisher_exists = in_array($publisher, $publisher_arr);
			if($publisher_exists)
			{
				updatePostCount($publisher, $count, $publisher_arr);
			}
			else
			{
				savePostCount($publisher, $count, "A new publisher! File updated and post count added!\n", 0);
			}
		}
		else
		{
			savePostCount($publisher, $count, "Publisher post history file created and a new post count added.\n");
		}
	}
