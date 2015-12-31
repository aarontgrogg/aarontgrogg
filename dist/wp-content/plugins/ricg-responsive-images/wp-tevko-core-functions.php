<?php
/**
 * Caches and returns the base URL of the uploads directory.
 *
 * @since 3.0.0
 * @access private
 *
 * @return string The base URL, cached.
 */
function _wp_upload_dir_baseurl() {
	static $baseurl = array();

	$blog_id = get_current_blog_id();

	if ( empty( $baseurl[$blog_id] ) ) {
		$uploads_dir = wp_upload_dir();
		$baseurl[$blog_id] = $uploads_dir['baseurl'];
	}

	return $baseurl[$blog_id];
}

/**
 * Get the image size as array from its meta data.
 *
 * Used for responsive images.
 *
 * @since 3.0.0
 * @access private
 *
 * @param string $size_name  Image size. Accepts any valid image size name ('thumbnail', 'medium', etc.).
 * @param array  $image_meta The image meta data.
 * @return array|bool Array of width and height values in pixels (in that order)
 *                    or false if the size doesn't exist.
 */
function _wp_get_image_size_from_meta( $size_name, $image_meta ) {
	if ( $size_name === 'full' ) {
		return array(
			absint( $image_meta['width'] ),
			absint( $image_meta['height'] ),
		);
	} elseif ( ! empty( $image_meta['sizes'][$size_name] ) ) {
		return array(
			absint( $image_meta['sizes'][$size_name]['width'] ),
			absint( $image_meta['sizes'][$size_name]['height'] ),
		);
	}

	return false;
}

/**
 * Retrieves the value for an image attachment's 'srcset' attribute.
 *
 * @since 3.0.0
 *
 * @see wp_calculate_image_srcset()
 *
 * @param int          $attachment_id Image attachment ID.
 * @param array|string $size          Optional. Image size. Accepts any valid image size, or an array of
 *                                    width and height values in pixels (in that order). Default 'medium'.
 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'.
 *                                    Default null.
 * @return string|bool A 'srcset' value string or false.
 */
function wp_get_attachment_image_srcset( $attachment_id, $size = 'medium', $image_meta = null ) {
	if ( ! $image = wp_get_attachment_image_src( $attachment_id, $size ) ) {
		return false;
	}

	if ( ! is_array( $image_meta ) ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
	}

	$image_src = $image[0];
	$size_array = array(
		absint( $image[1] ),
		absint( $image[2] )
	);

	return wp_calculate_image_srcset( $size_array, $image_src, $image_meta, $attachment_id );
}

/**
 * A helper function to calculate the image sources to include in a 'srcset' attribute.
 *
 * @since 3.0.0
 *
 * @param array  $size_array    Array of width and height values in pixels (in that order).
 * @param string $image_src     The 'src' of the image.
 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param int    $attachment_id Optional. The image attachment ID to pass to the filter. Default 0.
 * @return string|bool          The 'srcset' attribute value. False on error or when only one source exists.
 */
function wp_calculate_image_srcset( $size_array, $image_src, $image_meta, $attachment_id = 0 ) {
	if ( empty( $image_meta['sizes'] ) ) {
		return false;
	}

	$image_sizes = $image_meta['sizes'];

	// Get the width and height of the image.
	$image_width = (int) $size_array[0];
	$image_height = (int) $size_array[1];

	// Bail early if error/no width.
	if ( $image_width < 1 ) {
		return false;
	}

	$image_basename = wp_basename( $image_meta['file'] );
	$image_baseurl = _wp_upload_dir_baseurl();

	/*
	 * WordPress flattens animated GIFs into one frame when generating intermediate sizes.
	 * To avoid hiding animation in user content, if src is a full size GIF, a srcset attribute is not generated.
	 * If src is an intermediate size GIF, the full size is excluded from srcset to keep a flattened GIF from becoming animated.
	 */
	if ( ! isset( $image_sizes['thumbnail']['mime-type'] ) || 'image/gif' !== $image_sizes['thumbnail']['mime-type'] ) {
		$image_sizes['full'] = array(
			'width'  => $image_meta['width'],
			'height' => $image_meta['height'],
			'file'   => $image_basename,
		);
	} elseif ( strpos( $image_src, $image_meta['file'] ) ) {
		return false;
	}

	// Uploads are (or have been) in year/month sub-directories.
	if ( $image_basename !== $image_meta['file'] ) {
		$dirname = dirname( $image_meta['file'] );

		if ( $dirname !== '.' ) {
			$image_baseurl = trailingslashit( $image_baseurl ) . $dirname;
		}
	}

	$image_baseurl = trailingslashit( $image_baseurl );

	/*
	 * Images that have been edited in WordPress after being uploaded will
	 * contain a unique hash. Look for that hash and use it later to filter
	 * out images that are leftovers from previous versions.
	 */
	$image_edited = preg_match( '/-e[0-9]{13}/', wp_basename( $image_src ), $image_edit_hash );

	/**
	 * Filter the maximum image width to be included in a 'srcset' attribute.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $max_width  The maximum image width to be included in the 'srcset'. Default '1600'.
	 * @param array $size_array Array of width and height values in pixels (in that order).
	 */
	$max_srcset_image_width = apply_filters( 'max_srcset_image_width', 1600, $size_array );

	// Array to hold URL candidates.
	$sources = array();

	/*
	 * Loop through available images. Only use images that are resized
	 * versions of the same edit.
	 */
	foreach ( $image_sizes as $image ) {

		// Filter out images that are from previous edits.
		if ( $image_edited && ! strpos( $image['file'], $image_edit_hash[0] ) ) {
			continue;
		}

		// Filter out images that are wider than '$max_srcset_image_width'.
		if ( $max_srcset_image_width && $image['width'] > $max_srcset_image_width ) {
			continue;
		}

		/**
		 * To check for varying crops, we calculate the expected size of the smaller
		 * image if the larger were contstrained by the width of the smaller and then
		 * see if it matches what we're expecting.
		 */
		if ( $image_width > $image['width'] ) {
			$constrained_size = wp_constrain_dimensions( $image_width, $image_height, $image['width'] );
			$expected_size = array( $image['width'], $image['height'] );
		} else {
			$constrained_size = wp_constrain_dimensions( $image['width'], $image['height'], $image_width );
			$expected_size = array( $image_width, $image_height );
		}

		// If the image dimensions are within 1px of the expected size, use it.
		if ( abs( $constrained_size[0] - $expected_size[0] ) <= 1 && abs( $constrained_size[1] - $expected_size[1] ) <= 1 ) {
			// Add the URL, descriptor, and value to the sources array to be returned.
			$sources[ $image['width'] ] = array(
				'url'        => $image_baseurl . $image['file'],
				'descriptor' => 'w',
				'value'      => $image['width'],
			);
		}
	}

	/**
	 * Filter an image's 'srcset' sources.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID or 0.
	 */
	$sources = apply_filters( 'wp_calculate_image_srcset', $sources, $size_array, $image_src, $image_meta, $attachment_id );

	// Only return a 'srcset' value if there is more than one source.
	if ( count( $sources ) < 2 ) {
		return false;
	}

	$srcset = '';

	foreach ( $sources as $source ) {
		$srcset .= $source['url'] . ' ' . $source['value'] . $source['descriptor'] . ', ';
	}

	return rtrim( $srcset, ', ' );
}

/**
 * Retrieves the value for an image attachment's 'sizes' attribute.
 *
 * @since 3.0.0
 *
 * @see wp_calculate_image_sizes()
 *
 * @param int          $attachment_id Image attachment ID.
 * @param array|string $size          Optional. Image size. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'medium'.
 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'.
 *                                    Default null.
 * @return string|bool A valid source size value for use in a 'sizes' attribute or false.
 */
function wp_get_attachment_image_sizes( $attachment_id, $size = 'medium', $image_meta = null ) {
	if ( ! $image = wp_get_attachment_image_src( $attachment_id, $size ) ) {
		return false;
	}

	if ( ! is_array( $image_meta ) ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
	}

	$image_src = $image[0];
	$size_array = array(
		absint( $image[1] ),
		absint( $image[2] )
	);

	return wp_calculate_image_sizes( $size_array, $image_src, $image_meta, $attachment_id );
}

/**
 * Creates a 'sizes' attribute value for an image.
 *
 * @since 3.1.0
 *
 * @param array|string $size          Image size to retrieve. Accepts any valid image size, or an array
 *                                    of width and height values in pixels (in that order).
 * @param string       $image_src     Optional. The URL to the image file. Default null.
 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'. Default null.
 * @param int          $attachment_id Optional. Image attachment ID. Either `$image_meta` or `$attachment_id` is needed
 *                                    when using the image size name as argument for `$size`. Default 0.

 *
 * @return string|bool A valid source size value for use in a 'sizes' attribute or false.
 */
function wp_calculate_image_sizes( $size, $image_src = null, $image_meta = null, $attachment_id = 0 ) {
	$width = 0;

	if ( is_array( $size ) ) {
		$width = absint( $size[0] );
	} elseif ( is_string( $size ) ) {
		if ( ! $image_meta && $attachment_id ) {
			$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		}

		if ( is_array( $image_meta ) ) {
			$size_array = _wp_get_image_size_from_meta( $size, $image_meta );
			if ( $size_array ) {
				$width = absint( $size_array[0] );
			}
		}
	}

	if ( ! $width ) {
		return false;
	}

	// Setup the default 'sizes' attribute.
	$sizes = sprintf( '(max-width: %1$dpx) 100vw, %1$dpx', $width );

	/**
	 * Filter the output of 'wp_calculate_image_sizes()'.
	 *
	 * @since 3.1.0
	 *
	 * @param string       $sizes         A source size value for use in a 'sizes' attribute.
	 * @param array|string $size          Requested size. Image size or array of width and height values
	 *                                    in pixels (in that order).
	 * @param string|null  $image_src     The URL to the image file or null.
	 * @param array|null   $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()' or null.
	 * @param int          $attachment_id Image attachment ID of the original image or 0.
	 */
	return apply_filters( 'wp_calculate_image_sizes', $sizes, $size, $image_src, $image_meta, $attachment_id );
}

/**
 * Filters 'img' elements in post content to add 'srcset' and 'sizes' attributes.
 *
 * @since 3.0.0
 *
 * @see wp_image_add_srcset_and_sizes()
 *
 * @param string $content The raw post content to be filtered.
 * @return string Converted content with 'srcset' and 'sizes' attributes added to images.
 */
function wp_make_content_images_responsive( $content ) {
	if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) ) {
		return $content;
	}

	$selected_images = $attachment_ids = array();

	foreach( $matches[0] as $image ) {
		if ( false === strpos( $image, ' srcset=' ) && preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) &&
			( $attachment_id = absint( $class_id[1] ) ) ) {

			/*
			 * If exactly the same image tag is used more than once, overwrite it.
			 * All identical tags will be replaced later with 'str_replace()'.
			 */
			$selected_images[ $image ] = $attachment_id;
			// Overwrite the ID when the same image is included more than once.
			$attachment_ids[ $attachment_id ] = true;
		}
	}

	if ( count( $attachment_ids ) > 1 ) {
		/*
		 * Warm object cache for use with 'get_post_meta()'.
		 *
		 * To avoid making a database call for each image, a single query
		 * warms the object cache with the meta information for all images.
		 */
		update_meta_cache( 'post', array_keys( $attachment_ids ) );
	}

	foreach ( $selected_images as $image => $attachment_id ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		$content = str_replace( $image, wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ), $content );
	}

	return $content;
}
add_filter( 'the_content', 'wp_make_content_images_responsive', 5, 1 );

/**
 * Adds 'srcset' and 'sizes' attributes to an existing 'img' element.
 *
 * @since 3.0.0
 *
 * @see wp_calculate_image_srcset()
 * @see wp_calculate_image_sizes()
 *
 * @param string $image         An HTML 'img' element to be filtered.
 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param int    $attachment_id Image attachment ID.
 * @return string Converted 'img' element with 'srcset' and 'sizes' attributes added.
 */
function wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ) {
	// Ensure the image meta exists.
	if ( empty( $image_meta['sizes'] ) ) {
		return $image;
	}

	$image_src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';
	list( $image_src ) = explode( '?', $image_src );

	// Return early if we couldn't get the image source.
	if ( ! $image_src ) {
		return $image;
	}

	// Bail early if an image has been inserted and later edited.
	if ( preg_match( '/-e[0-9]{13}/', $image_meta['file'], $img_edit_hash ) &&
		strpos( wp_basename( $image_src ), $img_edit_hash[0] ) === false ) {

		return $image;
	}

	/**
	 * To make sure that our ID and image src match, we loop through all the sizes
	 * in our attachment metadata and bail early if our src file isn't included.
	 */
	$file_name = wp_basename( $image_src );

	$all_sizes = wp_list_pluck( $image_meta['sizes'], 'file' );
	$all_sizes[] = $image_meta['file'];

	$matched = false;

	foreach( $all_sizes as $size ) {
		if ( false !== strpos( $size, $file_name ) ) {
			$matched = true;
			break;
		}
	}

	// Bail early if the image src doesn't match any of the known image sizes.
	if ( ! $matched ) {
		return $image;
	}

	$width  = preg_match( '/ width="([0-9]+)"/',  $image, $match_width  ) ? (int) $match_width[1]  : 0;
	$height = preg_match( '/ height="([0-9]+)"/', $image, $match_height ) ? (int) $match_height[1] : 0;

	if ( ! $width || ! $height ) {
		/*
		 * If attempts to parse the size value failed, attempt to use the image meta data to match
		 * the image file name from 'src' against the available sizes for an attachment.
		 */
		$image_filename = wp_basename( $image_src );

		if ( $image_filename === wp_basename( $image_meta['file'] ) ) {
			$width = (int) $image_meta['width'];
			$height = (int) $image_meta['height'];
		} else {
			foreach( $image_meta['sizes'] as $image_size_data ) {
				if ( $image_filename === $image_size_data['file'] ) {
					$width = (int) $image_size_data['width'];
					$height = (int) $image_size_data['height'];
					break;
				}
			}
		}
	}

	if ( ! $width || ! $height ) {
		return $image;
	}

	$size_array = array( $width, $height );
	$srcset = wp_calculate_image_srcset( $size_array, $image_src, $image_meta, $attachment_id );

	if ( $srcset ) {
		// Check if there is already a 'sizes' attribute.
		$sizes = strpos( $image, ' sizes=' );

		if ( ! $sizes ) {
			$sizes = wp_calculate_image_sizes( $size_array, $image_src, $image_meta, $attachment_id );
		}
	}

	if ( $srcset && $sizes ) {
		// Format the 'srcset' and 'sizes' string and escape attributes.
		$attr = sprintf( ' srcset="%s"', esc_attr( $srcset ) );

		if ( is_string( $sizes ) ) {
			$attr .= sprintf( ' sizes="%s"', esc_attr( $sizes ) );
		}

		// Add 'srcset' and 'sizes' attributes to the image markup.
		$image = preg_replace( '/<img ([^>]+?)[\/ ]*>/', '<img $1' . $attr . ' />', $image );
	}

	return $image;
}

/*
 * Add the filter to add 'srcset' and 'sizes' attributes to post thumbnails and gallery images.
 * 'tevkori_filter_attachment_image_attributes()' can be found in wp-tevko-responsive-images.php
 */
add_filter( 'wp_get_attachment_image_attributes', 'tevkori_filter_attachment_image_attributes', 0, 3 );
