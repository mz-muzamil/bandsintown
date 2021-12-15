<?php

/**
 * Template Name: Home
 *
 * Template for displaying a blank page.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');

?>

<div class="main">
	<div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">
		<div class="row justify-content-center">
			<div class="col-xl-6">
				<div class="form-wrp">
					<form class="form-search-artist position-relative" id="form_search_artist" action="<?php echo get_permalink() ?>">
						<input class="form-control form-control-lg" placeholder="Search artist" type="text" data-rule-required name="name" id="artist_name_field" placeholder="Name" required>
						<button id="btn_search_artist" type="submit" value="Submit"><i class="fas fa-search"></i></button>
						<?php wp_nonce_field('submit_search_artist_form', 'artist_form_nonce'); ?>
						<input type="hidden" name="action" value="submit_search_artist_form" />
					</form>
				</div>
				<div class="w-100 artist_cards" id="artist_cards"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-xl-12">
				<div class="w-100" id="artist_events"></div>
			</div>
		</div>
	</div>
</div>
<div class="spinner-wrp d-none">
	<div class="lds-roller">
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>
	<div class="loader-spinner"></div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#btn_search_artist").on('click', function() {
			jQuery("#form_search_artist").validate({
				submitHandler: function() {
					var formData = jQuery("#form_search_artist").serialize();
					jQuery(".spinner-wrp").removeClass("d-none");
					jQuery.ajax({
						type: "POST",
						data: {
							action: 'submit_search_artist_form',
							'formData': formData,
						},
						url: '<?php echo esc_url(admin_url('admin-post.php')); ?>',
						success: function(data) {
							jQuery(".spinner-wrp").addClass("d-none");
							var artist_info = jQuery.parseJSON(data);
							var html = '';
							if (artist_info.error != "Not Found" && artist_info != "" && artist_info.message == null) {
								const {
									name,
									thumb_url,
									facebook_page_url
								} = artist_info;
								jQuery("#artist_cards, #form_search_artist").show();
								jQuery("#artist_events").hide();
								html += '<h4 class="mt-5"> Results founded for <small>"' + name + '"</small></h4>';
								html += '<form id="events_form" action="<?php echo get_permalink() ?>">';
								html += '<div class="card artist-card" onclick="fetchArtistEvents()">';
								html += '<div class="card-body">';
								html += '<figure id="get_events">';
								html += '<img src="' + thumb_url + '" />';
								html += '</figure>';
								html += '<div class="text">';
								html += '<h1>' + name + '</h1>';
								html += '<a target="_blank" href="' + facebook_page_url + '">' + facebook_page_url + '</a>';
								html += '</div>';
								html += '</div>';
								html += '<?php wp_nonce_field('submit_artist_events_form', 'artist_events_form_nonce'); ?>';
								html += '<input type="hidden" name="action" value="submit_artist_events_form" />';
								html += '<input type="hidden" name="artist_name" value="' + name + '" />';
								html += '</form>';
								html += '</div>';

							} else {
								html += '<h6 class="mt-5 text-center">No record found</h6>';
							}
							jQuery("#artist_cards").html(html);
						}
					});
				}

			});

		});
	});

	function backToResults() {
		jQuery("#btn_search_artist").click();
	}

	function fetchArtistEvents() {
		var formData = jQuery("#events_form").serialize();
		jQuery(".spinner-wrp").removeClass("d-none");
		jQuery.ajax({
			type: "POST",
			data: {
				action: 'submit_artist_events_form',
				'formData': formData,
			},
			url: '<?php echo esc_url(admin_url('admin-post.php')); ?>',
			success: function(data) {
				jQuery("#artist_cards, #form_search_artist").hide();
				jQuery("#artist_events").show();
				jQuery("#form_search_artist").hide();
				jQuery(".spinner-wrp").addClass("d-none");
				var events_info = jQuery.parseJSON(data);
				// console.log('events_info: ', events_info.datetime);

				var html = '';
				html += '<div class="row justify-content-center">';
				html += '<div class="col-xl-12">';
				html += '<h4 class="mt-5 mb-4 back-to-results">';
				html += '<a onclick="backToResults()" class="text-black" href="javascript:;"><i class="fas fa-angle-left"></i> Back to results</a>';
				html += '</h4>';
				html += '</div">';
				html += '</div">';
				html += '<div class="row justify-content-center">';
				if (events_info.length > 0) {
					jQuery.each(events_info, function(key, value) {
						if (value.artist && value.artist != "undefine") {
							const {
								name,
								thumb_url,
								facebook_page_url
							} = value.artist;
							html += '<div class="col-xl-12 mb-4 mt-4">';
							html += '<div class="row">';
							html += '<div class="col-xl-4">';
							html += '<div class="card artist-card">';
							html += '<div class="card-body">';
							html += '<figure id="get_events">';
							html += '<img src="' + thumb_url + '" />';
							html += '</figure>';
							html += '<div class="text">';
							html += '<h3>' + name + '</h3>';
							html += '<a target="_blank" href="' + facebook_page_url + '">' + facebook_page_url + '</a>';
							html += '</div>';
							html += '</div>';
							html += '</div>';
							html += '</div>';
							html += '</div>';
							html += '<h4 class="mt-5">' + events_info.length + ' upcoming events</h4>';
							html += '</div>';
						}

						const {
							location,
							name,
							city,
							country,
							region,
							type
						} = value.venue;

						html += '<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">';
						html += '<div class="card h-100">';
						html += '<div class="card-body">';
						html += '<h5 class="border-bottom pb-3 mb-3">EVENT DETAILS</h5>';
						html += '<div class="row">';

						html += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-3">';
						html += '<h5> Country </h5>';
						html += '<h6 class="text-muted">' + country + '</h6>';
						html += '</div>';

						html += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-3">';
						html += '<h5> City </h5>';
						html += '<h6 class="text-muted">' + city + '</h6>';
						html += '</div>';

						html += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">';
						html += '<h5> Venue </h5>';
						html += '<h6 class="text-muted">' + location + '</h6>';
						html += '</div>';

						html += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">';
						html += '<h5> Date </h5>';
						html += '<h6 class="text-muted">' + value.datetime + '</h6>';
						html += '</div>';

						html += '</div>';
						html += '</div>';
						html += '</div>';
						html += '</div>';
					});
				} else {
					html += '<h6 class="text-muted">Events not found</h6>';
				}

				html += '</div>';
				jQuery("#artist_events").html(html);
			}
		});
	}
</script>
<?php
get_footer();
