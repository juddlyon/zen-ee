<?php

		$encoding_specs = array(
			"test" => "$enable_test_mode == 'On' || $enable_test_mode == '' ? 'true' : 'false'",
			"input" => "' . $intput_url .'",
			"output" => array(
				"public" => 1,
				"filename" => "' . $filename . '.mp4",
				"device_profile" => "mobile/baseline",
				"base_url" => "' . $output_path . '",
				"format" => "mp4",
				"label" => "mp4",
				"video_codec" => "h264",
				"audio_codec" => "aac",
				"thumbnails" => array(
					"number" => 1,
					"format" => "jpg",
					"aspect_mode" => "crop",
				  	"size" => "' . $video_dimension . '",
				 	"base_url" => "' . $output_path . '",
					"filename" => "tn_' . $filename . '",
					"times" => ' . $thumb_time . '
				),
			"notifications" => array(
		 		"url" => "' . $update_job_status_url . '",
		  	"format" => "json"
			),
			"public" => 1,
			"filename" => "' . $filename . '.webm",
		 	"device_profile" => "mobile/baseline",
		 	"base_url" => "' . $output_path . '",
		 	"format" => "webm",
		 	"label" => "webm",
		 	"video_codec" => "vp8",
		 	"audio_codec" => "vorbis",
		 	"thumbnails" => array(
		  	"number" => 1,
		  	"format" => "jpg",
		  	"aspect_mode" => "crop",
		  	"size" => "' . $video_dimension . '",
			 	"base_url" => "' . $output_path . '",
				"filename" => "tn_' . $filename . '",
				"times" => ' . $thumb_time . '
		 	),
			"notifications" => array(
		 		"url" => "' . $update_job_status_url . '",
		  	"format" => "json"
			),
			)
		);


var_dump(json_encode($encoding_specs));