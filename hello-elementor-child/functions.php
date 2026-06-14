<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

error_log('testing log feature');

define( 'HELLO_CHILD_THEME_URL', get_stylesheet_directory_uri() );
define( 'HELLO_CHILD_THEME_IMAGES_URL', HELLO_CHILD_THEME_URL . '/assets/images/' );
$upload_dir = wp_upload_dir();
$uploads_url = $upload_dir['baseurl'];
define( 'HELLO_CHILD_WP_UPLOADS', $uploads_url);

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION
add_action('wp_enqueue_scripts', function () {
    //if (is_singular('speaking-session') || is_singular('ai-speaker')) {
        wp_enqueue_style(
            'speaking-session-loop-custom',
            get_stylesheet_directory_uri() . '/assets/css/speaking-session-loop.css',
            [],
            '1.0.0'
        );
    //}
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'header-glass',
        get_stylesheet_directory_uri() . '/assets/css/header-glass.css',
        [],
        '1.0.5'
    );

    wp_add_inline_script('jquery-core', "
        (function () {
            function initGlassHeader() {
                var header = document.querySelector('.elementor-location-header') || document.querySelector('.site-header');
                if (!header) return;

                var style = document.createElement('style');
                style.textContent = '.header-glass-active .e-parent > .e-con-inner > .e-child { box-shadow: 0 1px 0 rgba(255,255,255,0.6) inset, 0 8px 32px rgba(0,0,0,0.10) !important; }';
                document.head.appendChild(style);

                function toggle() {
                    header.classList.toggle('header-glass-active', window.scrollY > 30);
                }

                window.addEventListener('scroll', toggle, { passive: true });
                toggle();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initGlassHeader);
            } else {
                initGlassHeader();
            }
        })();
    ");
});


add_shortcode('toggle_function','toggle_function');
function toggle_function() {
    if (!empty($_GET['elementor-preview']))
		return;

    ?>
    <script>
        jQuery('.toggle-icon.toggle-open').click(function () {
            jQuery(this).hide();
            jQuery(this).siblings('.toggle-icon.toggle-close').show();
            jQuery(this).parents('.e-con-inner').find('.toggle-wrapper:hidden').siblings('.event-agenda-second-header').click();
        });
        jQuery('.toggle-icon.toggle-close').click(function () {
            jQuery(this).hide();
            jQuery(this).siblings('.toggle-icon.toggle-open').show();
            jQuery(this).parents('.e-con-inner').find('.toggle-wrapper:visible').siblings('.event-agenda-second-header').click();
        });
        jQuery('.event-agenda-second-header').click(function () {
            jQuery(this).siblings( ".toggle-wrapper").slideToggle();
            jQuery(this).find('.elementor-icon').toggleClass("icon-rotated");
        });
    </script>
    <?php
}

add_shortcode('faq_toggle_function','faq_toggle_function');
function faq_toggle_function() {
    if (!empty($_GET['elementor-preview']))
		return;

    ?>
    <script>
        jQuery('.toggle-icon.toggle-open').click(function () {
            jQuery(this).hide();
            jQuery(this).siblings('.toggle-icon.toggle-close').show();
            jQuery(this).parent('.elementor-element').parent('.e-con-inner').find('.elementor-tab-content:hidden').siblings('.elementor-tab-title').click();
        });
        jQuery('.toggle-icon.toggle-close').click(function () {
            jQuery(this).hide();
            jQuery(this).siblings('.toggle-icon.toggle-open').show();
            jQuery(this).parent('.elementor-element').parent('.e-con-inner').find('.elementor-tab-content:visible').siblings('.elementor-tab-title').click();
        });
    </script>
    <?php
}


add_shortcode('get_speaker_tabs_and_contents','get_speaker_tabs_and_contents');
function get_speaker_tabs_and_contents() {
    if (!empty($_GET['elementor-preview']))
		return;
	/**  Guarantee return_value variable always exists  */
    $return_value = '';
    $speaker_id =  get_the_ID();
    $tabs_Arr = get_post_meta($speaker_id,'cwp_field_885398582603',true);
    $tab_names = [];
    $tab_contents = [];
	if (is_array($tabs_Arr)) {
    foreach($tabs_Arr as $tab){
        $tab_names[] =  $tab['cwp_field_435702602076'];
    }
    $tab_names = array_unique($tab_names);

    foreach($tabs_Arr as $tab){
        $tab_contents[$tab['cwp_field_435702602076']][] = ['header'=>$tab['cwp_field_753187493681'],'content'=>$tab['cwp_field_507508441095'],'link'=>$tab['cwp_field_623043359226']];
    }
	$return_value = '<div id="speaker-tabs">';
	if(!empty($tab_names)){
		$return_value .= '<ul>';
		$i = 1;
		foreach($tab_names as $tab_name){
			if(!empty($tab_name)){
				$return_value .= "<li><a href='#speaker-tabs-$i'>$tab_name</a></li>";
			}
			$i++;
		}
		$return_value .= '</ul>';
	}
	}
	if(!empty($tab_contents)){
		$i = 1;
		$return_value .= '<div class="tab-content">';
		foreach($tab_contents as $tab_name => $tab_content){
			if(!empty($tab_name)){
				$return_value .= "<div id='speaker-tabs-$i'>";
				foreach($tab_content as $tab_data){
					$return_value .= "<div class='tab-content-wrapper'>";
					$return_value .= "
					<p class='tab-header'>".$tab_data['header']."</p>
					<p>".$tab_data['content']."</p>
					<a class='view-details d-inline-block' href='".$tab_data['link']."'>+ Learn more</a>";
					$return_value .= "</div>";
				}
				$i++;
				$return_value .= "</div>";
			}
		}
		$return_value .= '</div>';
	}
    if (is_array($tabs_Arr)) {
    $return_value .= '</div>';
    $return_value .= '
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script>
    $( function() {
      $( "#speaker-tabs" ).tabs();
    } );
    </script>';
    }
    return $return_value;
}
add_action('elementor/query/speaker-sessions', 'speaker_get_related_sessions');
function speaker_get_related_sessions($query)
{
	$meta_query = $query->get('meta_query');

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if (!$meta_query) {
		$meta_query = [];
	}
	$speaker_id = get_the_ID();
	$meta_query['relation'] = 'OR';
	$meta_query['speaker_id'] = array(
		'key' => 'cwp_field_227719618722',
		'value' => $speaker_id,
	);
	$meta_query['panelist_id'] = array(
		'key' => 'cwp_field_261211398069',
		'value' => $speaker_id,
	);
	$meta_query['moderator_id'] = array(
		'key' => 'cwp_field_480325353017',
		'value' => $speaker_id,
	);
	$query->set('meta_query', $meta_query);
}

add_action('wp_enqueue_scripts', function () {
    if (is_singular('ai-speaker')) {
        wp_add_inline_script(
          'jquery-core',
          "
          document.addEventListener('DOMContentLoaded', function () {
          const otherSessions = document.querySelector('.speaker-sessions-others');
          const title = document.querySelector('.other-sessions-title');

          if (otherSessions) {
            const checkItems = () => {
              const items = otherSessions.querySelectorAll('.e-loop-item');
              if (items.length === 0) {
                otherSessions.style.display = 'none';
              } else {
                otherSessions.style.display = '';
              }
            };

            checkItems();

            const observer = new MutationObserver(checkItems);
            observer.observe(otherSessions, { childList: true, subtree: true });
          }
          });
          "
        );
    }
});



add_action('wp_enqueue_scripts', function () {

    if (!is_singular('ai-speaker')) return;

    wp_add_inline_script(
        'jquery-core',
        "
        document.addEventListener('DOMContentLoaded', function () {

            // If browser supports :has(), do nothing
            //if (CSS.supports('selector(:has(*))')) return;

            const shortcode = document.querySelector('.speaker-video-wrapper .elementor-shortcode');
            const title = document.querySelector('.speaker-video-title');

            if (!shortcode) return;

            const isEmpty = shortcode.children.length === 0 && shortcode.textContent.trim() === '';

            if (isEmpty) {
                // If wrapped
                const wrapper = shortcode.closest('.speaker-video-wrapper');
                if (wrapper) {
                    wrapper.style.display = 'none';
                   wrapper.style.overflow = 'hidden';
    wrapper.style.margin = '0';
                } else if (title) {
                    title.style.display = 'none';
                    title.style.height = '0';
                     title.style.overflow = 'hidden';
    title.style.margin = '0';
                }
            }

        });
        "
    );

});


/**
 * Elementor query for speakers attached to a session
 * Also sets a global variable for "Host" if the session is a Workshop
 */
add_action('elementor/query/session-speaker', 'session_get_related_speaker');
function session_get_related_speaker($query)
{
	// --- Get the current Session ID ---
	$session_id = get_the_ID();
	if (!$session_id) return;

	// --- Attach speakers by meta field ---
	$speaker_ids = (array) get_post_meta($session_id, 'cwp_field_227719618722', true);
	if (empty($speaker_ids)) return;

	$query->set('post__in', $speaker_ids);

	// --- Check if this Session has "workshop" taxonomy ---
	$terms = wp_get_post_terms($session_id, 'speaking-session-category');
	if (!is_wp_error($terms) && !empty($terms)) {
		foreach ($terms as $term) {
			if (sanitize_title($term->slug) === 'workshop') {
				global $session_speaker_role;
				$session_speaker_role = 'Workshop Presenter';
				break;
			}
		}
	}
}

/**
 * Shortcode to display role label (e.g. "Host")
 * Use inside your Elementor speaker-loop template
 */
add_shortcode('get_speaker_role', function() {
	global $session_speaker_role;
	if (!empty($session_speaker_role)) {
		return '<span class="speaker-role-label">' . esc_html($session_speaker_role) . '</span>';
	}
	return '';
});



add_action('elementor/query/session-cohost', 'session_get_related_cohost');
function session_get_related_cohost($query)
{
	// Get existing meta query just in case
	$meta_query = $query->get('meta_query');
	if (!$meta_query) {
		$meta_query = [];
	}

	// Get current session ID
	$session_id = get_the_ID();
	if (!$session_id) {
		// Prevent unintended global query if not in context
		$query->set('post__in', [0]);
		return;
	}

	// Get selected co-host IDs
	$cohost_ids = get_post_meta($session_id, 'cwp_field_227719618722co', true);

	// Normalize value (ACF/MetaBox can return scalar or array)
	if (!is_array($cohost_ids)) {
		if (empty($cohost_ids)) $cohost_ids = [];
		else $cohost_ids = [$cohost_ids];
	}

	// Fix: If empty, force query to return nothing
	if (empty($cohost_ids)) {
		$query->set('post__in', [0]); // forces no results
		return;
	}

	// Otherwise, set only the selected cohosts
	$query->set('post__in', $cohost_ids);
}


add_action('elementor/query/session-panelist', 'session_get_related_panelists');
function session_get_related_panelists($query)
{
	$meta_query = $query->get('meta_query');

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if (!$meta_query) {
		$meta_query = [];
	}
	$session_id = get_the_ID();
	$panelist_ids = get_post_meta($session_id,'cwp_field_261211398069',true);
	$query->set('post__in', $panelist_ids);
}

add_action('elementor/query/session-moderator', 'session_get_related_moderator');
function session_get_related_moderator($query)
{
	$meta_query = $query->get('meta_query');

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if (!$meta_query) {
		$meta_query = [];
	}
	$session_id = get_the_ID();
	$moderator_id = get_post_meta($session_id,'cwp_field_480325353017',true);
	$query->set('post__in', array($moderator_id ));
}

add_shortcode('hide_zoom_qa_link','hide_zoom_qa_link');
function hide_zoom_qa_link() {
    if (!empty($_GET['elementor-preview']))
		return;
 	$session_id =  get_the_ID();
	$zoom_link = get_post_meta($session_id,'cwp_field_15581632866',true);
	if(empty($zoom_link)){
		return '<script>
			jQuery("#'.$session_id.'.session-loop-zoom-link").hide();
		</script>';
	}
	$qa_link = get_post_meta($session_id,'cwp_field_394869217799',true);
	if(empty($qa_link)){
		return '<script>
			jQuery("#'.$session_id.'.session-loop-qa-link").hide();
		</script>';
	}
}

add_shortcode('get_session_tags','get_session_tags');
function get_session_tags() {
	
    if (!empty($_GET['elementor-preview']))
		return;
	
	$return_value = '';
    $session_id =  get_the_ID();
	$session_taxonomy = 'speaking-session-tag';
	$terms_list = wp_get_post_terms($session_id,$session_taxonomy);
	if($terms_list){
		foreach($terms_list as $term){
			$return_value .= "<div class='session-tag $term->name'>$term->name</div>";
		}
	}
	else{
		return;
	}
	
    return $return_value;
}

add_shortcode('get_session_speakers_link','get_session_speakers_link');
function get_session_speakers_link() {
	
    if (!empty($_GET['elementor-preview'] ) || (!empty($_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
		return;	
	}
	
	/**  Guarantee the return_value variable always exists  */
	$return_value = '';
    $session_id =  get_the_ID();
	$term_ids = wp_get_post_terms($session_id,'speaking-session-category',array( 'fields' => 'ids' ));
	$speaker_id = get_post_meta($session_id,'cwp_field_227719618722',true);
	if(!empty($speaker_id)){
		$speaker_name = get_the_title($speaker_id);
		$speaker_link = get_permalink($speaker_id);
		
		if($term_ids[0] == '57'){
			$return_value .= "<p>Workshop Presenter: </p>";
		}
		$speaker_company = get_post_meta($speaker_id,'cwp_field_933137458361',true);
		if(!empty($speaker_company)){
			$return_value .= "<a href='$speaker_link'>$speaker_name , $speaker_company</a>";
		}
		else{
			$return_value .= "<a href='$speaker_link'>$speaker_name</a>";
		}
	}
	$panelist_ids = get_post_meta($session_id,'cwp_field_261211398069',true);
	$moderator_id = get_post_meta($session_id,'cwp_field_480325353017',true);
	if(is_array($panelist_ids)){
		$panelist_ids = array_filter($panelist_ids);
	}
	if(!empty($panelist_ids)){
		$total = count($panelist_ids);
		$return_value .= "<div class='panelists-wrapper'>";
		$return_value .= "<span>Panelists: </span>";
		$count = 0;
		foreach($panelist_ids as $panelist_id){
			$panelist_name = get_the_title($panelist_id);
			$panelist_link = get_permalink($panelist_id);
			$panelist_company = get_post_meta($panelist_id,'cwp_field_933137458361',true);
			if(!empty($panelist_company)){
				$return_value .= "<a href='$panelist_link'>$panelist_name , $panelist_company</a>";
			}
			else{
				$return_value .= "<a href='$panelist_link'>$panelist_name</a>";
			}
			if(++$count === $total) {
				$return_value .= "</div>  <div>";
			}
			else{
				$return_value .= "<span> | </span>";
			}
		}
		if($moderator_id){
			$moderator_name = get_the_title($moderator_id);
			$moderator_link = get_permalink($moderator_id);
			$moderator_company = get_post_meta($moderator_id,'cwp_field_933137458361',true);
			$return_value .= "<span>Moderator: </span>";
			if(!empty($moderator_company)){
				$return_value .= "<a href='$moderator_link'>$moderator_name , $moderator_company</a>";
			}
			else{
				$return_value .= "<a href='$moderator_link'>$moderator_name</a>";
			}
		}
		$return_value .= "</div>";
	}

    return $return_value;
}

add_shortcode('get_session_zoom_link','get_session_zoom_link');
function get_session_zoom_link() {
	
    if (!empty($_GET['elementor-preview']))
		return;
	
	$return_value = '';
	$session_id =  get_the_ID();
	$session_zoom_link = get_post_meta($session_id,'cwp_field_15581632866',true);
	if($session_zoom_link){
		return "Watch live broadcast on <a href='$session_zoom_link'>Zoom</a>";
	}
	else{
		return "None";
	}
    
}

add_shortcode('get_session_speaker_company_title','get_session_speaker_company_title');
function get_session_speaker_company_title() {
	
    if (!empty($_GET['elementor-preview']))
		return;
	
	$return_value = '';
    $speaker_id =  get_the_ID();
	$speaker_company_title = get_post_meta($speaker_id,'cwp_field_736210254370',true);
    return $speaker_company_title;
}

add_shortcode('get_session_speaker_company_logo','get_session_speaker_company_logo');
function get_session_speaker_company_logo() {
	
    if (!empty($_GET['elementor-preview']))
		return;
	
	$return_value = '';
    $speaker_id =  get_the_ID();
	$speaker_company_logo_id = get_post_meta($speaker_id,'cwp_field_198570236854',true);
	$speaker_company_logo = wp_get_attachment_image_url($speaker_company_logo_id,'full');
    return "<img src='$speaker_company_logo' />";
}
add_action( 'elementor/query/get-sessions-by-exclude-id', 'get_sessions_by_exclude_id' );
function get_sessions_by_exclude_id( $query ) {
	// Modify the posts query here
    $selected_post_id = $query->get('post__not_in');
    
    $query->set('post__not_in', array());
    $query->set('post__in', $selected_post_id);

}

add_shortcode('speaker_sessions','speaker_sessions');
function speaker_sessions()
{
	if (!empty($_GET['elementor-preview']))
		return;

	$custom_js_vars = 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
	echo "<script> $custom_js_vars </script>";
	
	$speaker_session_results = '';
	$panel_session_results = '';
	$industries = [];
	$speaker_args = array(
		'post_type' => 'speaking-session',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'speaking-session-category',
				'field' => 'term_id',
				'terms' => 47,
			),
		)
	);
	
	$speaker_query = new WP_Query($speaker_args);
	while ($speaker_query->have_posts()) {
		$speaker_query->the_post();
		$id = get_the_ID();
		$title = get_the_title();
		$industries_title = get_field_value( 'cwp_field_263473300547', false, $id );
		$industries[$industries_title] = get_post_meta($id,'cwp_field_263473300547',true);
		$speaker_session_results .= '<div class="session-wrapper">';
		$speaker_session_results .= do_shortcode('[elementor-template id="5515"]');
		$speaker_session_results .= '</div>';
	}
	wp_reset_query();

	$panel_args = array(
		'post_type' => 'speaking-session',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'speaking-session-category',
				'field' => 'term_id',
				'terms' => 48,
			),
		)
	);
	
	$panel_query = new WP_Query($panel_args);
	while ($panel_query->have_posts()) {
		$panel_query->the_post();
		$id = get_the_ID();
		$title = get_the_title();
		$industries_title = get_field_value( 'cwp_field_263473300547', false, $id );
		$industries[$industries_title] = get_post_meta($id,'cwp_field_263473300547',true);
		$panel_session_results .= '<div class="session-wrapper">';
		$panel_session_results .= do_shortcode('[elementor-template id="5515"]');
		$panel_session_results .= '</div>';
	}
	wp_reset_query();
	
	$wide_panel_args = array(
		'post_type' => 'speaking-session',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'speaking-session-category',
				'field' => 'term_id',
				'terms' => 56,
			),
		)
	);
	$wide_panel_session_results = '';
	$wide_panel_query = new WP_Query($wide_panel_args);
	while ($wide_panel_query->have_posts()) {
		$wide_panel_query->the_post();
		$id = get_the_ID();
		$title = get_the_title();
		$industries_title = get_field_value( 'cwp_field_263473300547', false, $id );
		$industries[$industries_title] = get_post_meta($id,'cwp_field_263473300547',true);
		$wide_panel_session_results .= '<div class="session-wrapper">';
		$wide_panel_session_results .= do_shortcode('[elementor-template id="5515"]');
		$wide_panel_session_results .= '</div>';
	}
	wp_reset_query();
	
	$workshop_args = array(
		'post_type' => 'speaking-session',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'speaking-session-category',
				'field' => 'term_id',
				'terms' => 57,
			),
		)
	);
	$workshop_session_results = '';
	$workshop_query = new WP_Query($workshop_args);
	while ($workshop_query->have_posts()) {
		$workshop_query->the_post();
		$id = get_the_ID();
		$title = get_the_title();
		$industries_title = get_field_value( 'cwp_field_263473300547', false, $id );
		$industries[$industries_title] = get_post_meta($id,'cwp_field_263473300547',true);
		$workshop_session_results .= '<div class="session-wrapper">';
		$workshop_session_results .= do_shortcode('[elementor-template id="5515"]');
		$workshop_session_results .= '</div>';
	}
	wp_reset_query();
	
	$category_options = '<option value="All">All</option>';
	$categories = get_terms( array(
		'taxonomy'   => 'speaking-session-category',
		'hide_empty' => false,
	) );
	foreach($categories as $category){
		$term_id = $category->term_id;
		$term_name = $category->name;
		if(!empty($term_id)){
			$category_options .= "<option value='$term_id'>$term_name</option>";
		}
	}

	$track_options = '<option value="All">All</option>';
	$tracks = get_terms( array(
		'taxonomy'   => 'speaking-session-tag',
		'hide_empty' => false,
	) );
	foreach($tracks as $track){
		$term_id = $track->term_id;
		$term_name = $track->name;
		if(!empty($term_id)){
			$track_options .= "<option value='$term_id'>$term_name</option>";
		}
	}
	$industry_options = '<option value="All">All</option>';
	$industries = array_unique($industries);
	$industries = array_filter($industries);
	foreach($industries as $industry_title => $industry_value){
		$industry_options .= "<option value='$industry_value'>$industry_title</option>";
	}
	?>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
	<script>
	</script>
	
	<div id="session-filter">
		<div class='filter-wrapper'>
			<div>
				<label>Content Type</label>
				<select name="category" onchange="filtering()">
					<?php echo $category_options; ?>
				</select>
			</div>
			<div>
				<label>Track</label>
				<select name="track" onchange="filtering()">
					<?php echo $track_options; ?>
				</select>
			</div>
			<div>
				<label>Industry</label>
				<select name="industry" onchange="filtering()">
					<?php echo $industry_options; ?>
				</select>
			</div>
			<img class='loading-image' src="https://techequity-ai.org/wp-content/uploads/2024/09/loading-greybg.gif" />
		</div>
		<div id="session-result">
			<h2>Speaker Sessions</h2>
			<div class="sessions-wrapper">
				<?php echo $speaker_session_results; ?>
			</div>
			<h2>Panel Discussions</h2>
			<div class="sessions-wrapper">
				<?php echo $panel_session_results; ?>
			</div>
			<h2>Workshop</h2>
			<div class="sessions-wrapper">
				<?php echo $workshop_session_results; ?>
			</div>
			<h2>Wide Panel Discussion</h2>
			<div class="sessions-wrapper">
				<?php echo $wide_panel_session_results; ?>
			</div>
		</div>
	</div>
	<script>
		function filtering(){
			jQuery('.loading-image').css('display','block');
			var category = jQuery('select[name="category"]').val();
			var track = jQuery('select[name="track"]').val();
			var industry = jQuery('select[name="industry"]').val();
			var data = {
					"action": "filterSession",
					"category": category,
					"track": track,
					"industry": industry,
				};
			jQuery.post(ajaxurl, data, function(response) {
				if (response && response.length) {
					jQuery('#session-result').empty();
					jQuery('#session-result').append(response);
					jQuery('.loading-image').css('display','none');
				}
			});
		}	
	</script>
	<?php
}
add_action("wp_ajax_nopriv_filterSession", "filterSession");
add_action('wp_ajax_filterSession', 'filterSession');
function filterSession(){
	$session_results = '';
	$is_show_category = '';
	$category = $_POST['category'];
	$track = $_POST['track'];
	$industry = $_POST['industry'];

	$args = array(
		'post_type' => 'speaking-session',
		'posts_per_page' => -1,
	);


	if($track && $track != 'All'){
		$args['tax_query'][] = 
			array(
				'taxonomy' => 'speaking-session-tag',
				'field' => 'term_id',
				'terms' => $track,
			);
	}
	if($industry && $industry != 'All'){
		$args['meta_key'] = 'cwp_field_263473300547';
		$args['meta_value'] = $industry;
		$args['meta_compare'] = '=';
	}
	if($category && $category != 'All'){
		$is_show_category = $category;
	}

	$speaker_args = $args ;
	$speaker_args['tax_query'][] = array(
		'taxonomy' => 'speaking-session-category',
		'field' => 'term_id',
		'terms' => 47,
	);
	$panel_args = $args ;
	$panel_args['tax_query'][] = array(
		'taxonomy' => 'speaking-session-category',
		'field' => 'term_id',
		'terms' => 48,
	);
	$wide_panel_args = $args ;
	$wide_panel_args['tax_query'][] = array(
		'taxonomy' => 'speaking-session-category',
		'field' => 'term_id',
		'terms' => 56,
	);
	$workshop_args = $args ;
	$workshop_args['tax_query'][] = array(
		'taxonomy' => 'speaking-session-category',
		'field' => 'term_id',
		'terms' => 57,
	);
	if($is_show_category == 47 || empty($is_show_category)){
		$session_results .= "<h2>Speaker Sessions</h2>";
		$session_results .= "<div class='sessions-wrapper'>";
		$speaker_query = new WP_Query($speaker_args);
		if($speaker_query->have_posts()){
			while ($speaker_query->have_posts()) {
				$speaker_query->the_post();
				$session_results .= '<div class="session-wrapper">';
				$session_results .= do_shortcode('[elementor-template id="5515"]');
				$session_results .= '</div>';
			}
		}
		else{
			$session_results .= "No results";
		}
		$session_results .= "</div>";
	}
	
	if($is_show_category == 48 || empty($is_show_category)){
		$session_results .= "<h2>Panel Discussions</h2>";
		$session_results .= "<div class='sessions-wrapper'>";
		$panel_query = new WP_Query($panel_args);
		if($panel_query->have_posts()){
			while ($panel_query->have_posts()) {
				$panel_query->the_post();
				$session_results .= '<div class="session-wrapper">';
				$session_results .= do_shortcode('[elementor-template id="5515"]');
				$session_results .= '</div>';
			}
		}
		else{
			$session_results .= "No results";
		}
		$session_results .= "</div>";
	}
	
	if($is_show_category == 57 || empty($is_show_category)){
		$session_results .= "<h2>Workshop</h2>";
		$session_results .= "<div class='sessions-wrapper'>";
		$workshop_query = new WP_Query($workshop_args);
		if($workshop_query->have_posts()){
			while ($workshop_query->have_posts()) {
				$workshop_query->the_post();
				$session_results .= '<div class="session-wrapper">';
				$session_results .= do_shortcode('[elementor-template id="5515"]');
				$session_results .= '</div>';
			}
		}
		else{
			$session_results .= "No results";
		}
		$session_results .= "</div>";
	}
	
	if($is_show_category == 56 || empty($is_show_category)){
		$session_results .= "<h2>Wide Panel Discussion</h2>";
		$session_results .= "<div class='sessions-wrapper'>";
		$wide_panel_query = new WP_Query($wide_panel_args);
		if($wide_panel_query->have_posts()){
			while ($wide_panel_query->have_posts()) {
				$wide_panel_query->the_post();
				$session_results .= '<div class="session-wrapper">';
				$session_results .= do_shortcode('[elementor-template id="5515"]');
				$session_results .= '</div>';
			}
		}
		else{
			$session_results .= "No results";
		}
		$session_results .= "</div>";
	}
	
	
	wp_reset_query();

	echo $session_results;
	wp_die();
}

add_action( 'admin_head', 'speaker_option_order' );
function speaker_option_order() {
    $screen = get_current_screen();
    if ( $screen->post_type == "speaking-session" ) {
    ?>
    <script type="text/javascript">
//         jQuery(document).ready(function ($) {
// 			var allOptions = $("#cwp_field_506479300642 option");
// 			allOptions.sort(function (op1, op2) {
// 				var text1 = $(op1).text().toLowerCase();
// 				var text2 = $(op2).text().toLowerCase();
// 				return (text1 < text2) ? -1 : 1;
// 			});
// 			allOptions.appendTo("#cwp_field_506479300642");
//         });
    </script>
    <?php
    }
}




// Summit Fall 2025

add_action('wp_enqueue_scripts', function () {
    global $post;
    if (has_shortcode($post->post_content, 'filter_agenda') ||
            has_shortcode($post->post_content, 'filter_ai_speakers')) {
        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
    }
});


// === Shortcode (filter_ai_speakers; works with Select2) ===========================
add_shortcode('filter_ai_speakers', function($atts) {
    if (!empty($_GET['elementor-preview'])) return;

   $atts = shortcode_atts([
        'speaker-type' => 'fall-2025-speakers',
        'init-count'   => '' // NEW (empty => show all)
    ], $atts);

    $year = esc_attr($atts['speaker-type']);

    $initCount = is_numeric($atts['init-count']) ? absint($atts['init-count']) : 0;

    ob_start();
    ?>
    <script>var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";</script>
    <div id="speaker-filter">
        <div class="filter-grid-wrapper" style="display:flex;flex-direction:column;">

            <!-- Day Radios from child terms of speaker-type: fall-2025-speakers -->
            <?php
            $parent = get_term_by('slug', 'fall-2025-speakers', 'speaker-type');
            $subterms = $parent ? get_terms([
                'taxonomy' => 'speaker-type',
                'hide_empty' => false,
                'parent' => $parent->term_id
            ]) : [];
            ?>
            <div class="speaker-days">
                <label style="display:none;">Day</label>
                <label class="active">
                    <input type="radio" name="speaker_day_term" value="">
                    <h4>All</h4>
                </label>
                <?php foreach ($subterms as $term): ?>
                    <label>
                        <input type="radio" name="speaker_day_term" value="<?= esc_attr($term->term_id); ?>">
                        <h4><?= esc_html($term->name); ?></h4>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="speaker-selectors elementor-loop-container elementor-widget-loop-grid-mobile-2 elementor-grid">
                <!-- Search -->
                <div class="filter-item-search">
                    <label>Search</label>
                    <input type="text" name="keyword" placeholder="Search..." />
                </div>

                <div class="m-row">
                    <div>
                        <label>AI Track</label>
                        <select name="ai-tracks">
                            <option value="">All</option>
                            <?php foreach (get_terms(['taxonomy' => 'ai-tracks', 'hide_empty' => false]) as $term) {
                                echo "<option value='".esc_attr($term->term_id)."'>".esc_html($term->name)."</option>";
                            } ?>
                        </select>
                    </div>

                    <div>
                        <label>AI Theme</label>
                        <select name="speaking-session-tag">
                            <option value="">All</option>
                            <?php foreach (get_terms(['taxonomy' => 'speaking-session-tag', 'hide_empty' => false]) as $term) {
                                echo "<option value='".esc_attr($term->term_id)."'>".esc_html($term->name)."</option>";
                            } ?>
                        </select>
                    </div>

                    <div>
                        <label>Content Type</label>
                        <select name="speaking-session-category">
                            <option value="">All</option>
                            <?php foreach (get_terms(['taxonomy' => 'speaking-session-category', 'hide_empty' => false]) as $term) {
                                echo "<option value='".esc_attr($term->term_id)."'>".esc_html($term->name)."</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </div>

            <img class="loading-image" src="<?php echo esc_url(HELLO_CHILD_WP_UPLOADS.'/2024/09/loading-greybg.gif'); ?>" />
        </div>

        <div id="speaker-results"></div>
    </div>

    <style>
      #speaker-filter {overflow:hidden;}
      #speaker-filter .filter-grid-wrapper { flex-direction: column; align-items: center; display: flex; margin-bottom: 50px; }
      #speaker-filter .speaker-days { margin-bottom: 45px; display: flex; }
      #speaker-filter .speaker-days h4 { font-family: Lora;font-style:italic; margin: 0 16px; font-size: 18px; cursor: pointer; font-weight: 500;letter-spacing:-0.02em; }
      #speaker-filter .speaker-days .active h4 {text-decoration: underline;
    text-decoration-thickness: 0.5px;
    text-underline-offset: 12px; }
      #speaker-filter .speaker-days input[type="radio"] { appearance: none; }
      #speaker-filter .speaker-selectors { grid-template-columns: 1fr 3fr; gap: 26px; max-width: 100vw;}
      #speaker-filter .m-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 26px; }
      #speaker-filter input[type=text] { padding: 13px 15px; border: 1px solid #262528; border-radius: 4px; background: transparent; height: 44px; width: 100%; font-size: 16px; color: #262528; min-width: 200px; background-image: url(<?php echo esc_url(HELLO_CHILD_THEME_IMAGES_URL.'/icon-search.svg'); ?>); background-repeat:no-repeat; background-position:right 10px center; }
      #speaker-filter label { font-family: var(--e-global-typography-55165a4-font-family), "IBM Plex Sans", Sans-serif; font-size: var(--e-global-typography-55165a4-font-size); font-weight: var(--e-global-typography-55165a4-font-weight); line-height: var(--e-global-typography-55165a4-line-height); color: var(--e-global-color-text); margin-bottom: 5px; }
      #speaker-filter .filter-grid-wrapper select { appearance: none; max-width: 280px; min-width: 280px; padding: 13px 24px; border: 1px solid #262528; border-radius: 4px; background: transparent; background-image: url(<?php echo esc_url(HELLO_CHILD_WP_UPLOADS.'/2024/09/AISummit-Select_without_border.svg'); ?>); background-repeat: no-repeat; background-size: 32px; background-position: right 20px center; color: #908F91; }
      .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; width: 35px; background-image: url(<?php echo esc_url(HELLO_CHILD_THEME_IMAGES_URL.'/icon-chevron-down.svg'); ?>); background-size: 22px; background-position: center center; background-repeat: no-repeat; }
      .select2-container--default .select2-selection--single .select2-selection__arrow b { display:none; }
      .select2-dropdown.select2-dropdown--below { top:32px!important; }
      .selection .select2-selection { height: 44px; }
      .select2-container--default .select2-selection--single { border-radius: 4px; border: 1px solid #262528; background-color: transparent;}
      .select2-container .select2-selection--single .select2-selection__rendered { padding-left: 15px; color: #262528; }
      @media (max-width: 1024px) {
        #speaker-filter .speaker-selectors .m-row { grid-template-columns: repeat(3, minmax(0, 1fr)); grid-template-rows: auto auto; grid-column: 1 / 3; }
        #speaker-filter .filter-item-search { grid-column: 1 / -1; }
        #speaker-filter .select2-container { max-width: 100%; }
      }
      @media (max-width: 600px) {
        #speaker-filter .m-row { gap: 10px; }
        #speaker-filter .speaker-days h4 { margin: 0 6px; }
        #speaker-filter .speaker-days { margin-bottom: 20px; }
        #speaker-filter .filter-grid-wrapper { margin-bottom: 30px; }
      }
      @media (min-width: 768px) { #speaker-filter .speaker-days h4 { font-size: 32px; } }
    </style>

    <script>
    // init-count from shortcode (0 = unlimited)
    const INIT_COUNT = <?php echo (int) $initCount; ?>;
    let firstLoad = true;

    // single-active for dropdowns / search, day persists
    const selectorNames = ['ai-tracks','speaking-session-tag','speaking-session-category','keyword'];
    const isSelector = (name) => selectorNames.includes(name);

    function resetSelectorsExcept(nameToKeep){
      const $ = jQuery;
      $('#speaker-filter select').each(function(){
        const n = $(this).attr('name');
        if (n !== nameToKeep) $(this).val('').trigger('change.select2');
      });
      if (nameToKeep !== 'keyword'){ $('input[name="keyword"]').val(''); }
    }

    function filterSpeakers(activeFilter) {
      const $ = jQuery;
      $('.loading-image').show();

      if (isSelector(activeFilter)) resetSelectorsExcept(activeFilter);


      const isUserAction = (activeFilter && activeFilter !== '');
      const limit = (firstLoad && !isUserAction && INIT_COUNT > 0) ? INIT_COUNT : -1;

      const data = {
        action: 'filterSpeakers',
        post_type: 'ai-speaker',
        track  : $('select[name="ai-tracks"]').val(),
        theme  : $('select[name="speaking-session-tag"]').val(),
        type   : $('select[name="speaking-session-category"]').val(),
        keyword: $('input[name="keyword"]').val(),
        speaker_day_term: $('input[name="speaker_day_term"]:checked').val() || '',
        limit: limit,
      };

      $.post(ajaxurl, data, function(res) {
        $('#speaker-results').html(res);
        $('.loading-image').hide();
        firstLoad = false; // after first response, never “init limit” again
      });
    }

    jQuery(function($){
      if ($.fn.select2) {
        $('#speaker-filter select').select2({ width: 'resolve', minimumResultsForSearch: Infinity });
      }

      // Dropdowns
      $('select[name="ai-tracks"]').on('select2:select change', function(){ filterSpeakers('ai-tracks'); });
      $('select[name="speaking-session-tag"]').on('select2:select change', function(){ filterSpeakers('speaking-session-tag'); });
      $('select[name="speaking-session-category"]').on('select2:select change', function(){ filterSpeakers('speaking-session-category'); });

      // Day radios (keep active class)
      $('input[name="speaker_day_term"]').on('change', function(){
        $(this).closest('label').addClass('active').siblings().removeClass('active');
        filterSpeakers('speaker_day_term');
      });

      // Search
      $('input[name="keyword"]').on('input', function(){ filterSpeakers('keyword'); });

      // Initial
      filterSpeakers('');
    });
    </script>
    <?php
    return ob_get_clean();
});


// === AJAX: filterSpeakers ================================================

function _cwp_multi_select_term_match($meta_key, $term_id){
    $tid = (string) absint($term_id);
    return [
        'relation' => 'OR',
        [
            'key'     => $meta_key,
            'value'   => '"' . $tid . '"',
            'compare' => 'LIKE',
        ],
        [
            'key'     => $meta_key,
            'value'   => ',' . $tid . ',',
            'compare' => 'LIKE',
        ],
        [
            'key'     => $meta_key,
            'value'   => $tid . ',',
            'compare' => 'LIKE',
        ],
        [
            'key'     => $meta_key,
            'value'   => ',' . $tid,
            'compare' => 'LIKE',
        ],
        [
            'key'     => $meta_key,
            'value'   => $tid,
            'compare' => '=',
            'type'    => 'CHAR',
        ],
    ];
}

function filterSpeakers() {
    if (empty($_POST['post_type']) || $_POST['post_type'] !== 'ai-speaker') wp_die();

    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : -1;
    if ($limit === 0) $limit = -1; // treat 0 as "all"

    $args = [
        'post_type'      => 'ai-speaker',
        // 'posts_per_page' => -1,
        'posts_per_page' => $limit, 
        // always constrain to the parent year "fall-2025-speakers"
        'tax_query'      => [
            'relation' => 'AND',
            [
                'taxonomy' => 'speaker-type',
                'field'    => 'slug',
                'terms'    => 'fall-2025-speakers',
                'operator' => 'IN',
            ],
        ],
    ];

    $tax_query  = [];
    $meta_query = [];

    // === NEW: meta fields that mirror taxonomies from "Speaker sessions" ===
    // Content Type → cwp_field_750783599718  (select taxonomy: speaking-session-category)
    if (!empty($_POST['type'])) {
        $tid = absint($_POST['type']);
        $meta_query[] = _cwp_multi_select_term_match('cwp_field_7507835997', $tid);
    }

    // AI Track → cwp_field_579027781260 (select taxonomy: ai-tracks)
    if (!empty($_POST['track'])) {
        $tid = absint($_POST['track']);
        $meta_query[] = _cwp_multi_select_term_match('cwp_field_5790277812', $tid);
    }

    // AI Theme → cwp_field_964452184973 (select taxonomy: speaking-session-tag)
    if (!empty($_POST['theme'])) {
        $tid = absint($_POST['theme']);
        $meta_query[] = _cwp_multi_select_term_match('cwp_field_9644521849', $tid);
    }

    // Day (child of speaker-type) still via taxonomy
    if (!empty($_POST['speaker_day_term'])) {
        $tax_query[] = [
            'taxonomy' => 'speaker-type',
            'field'    => 'term_id',
            'terms'    => absint($_POST['speaker_day_term']),
            'operator' => 'IN',
        ];
    }

    // Keyword
    if (!empty($_POST['keyword'])) {
        $args['s'] = sanitize_text_field($_POST['keyword']);
    }

    if (!empty($tax_query))  { $args['tax_query']  = array_merge($args['tax_query'], $tax_query); }
    if (!empty($meta_query)) { $args['meta_query'] = $meta_query; }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="elementor-loop-container elementor-widget-loop-grid-mobile-2 elementor-grid speakers-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            echo do_shortcode('[elementor-template id="956"]');
        }
        echo '<style>
            .speakers-grid { grid-template-columns: repeat(2, 1fr); display: grid; gap: 30px; }
            @media (min-width: 992px) { .speakers-grid { grid-template-columns: repeat(5, 1fr); gap: 20px; } }
            @media (min-width: 1190px) { .speakers-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
        </style>';
        echo '</div>';
    } else {
        echo '<p>No speakers found.</p>';
    }

    wp_die();
}
add_action('wp_ajax_filterSpeakers', 'filterSpeakers');
add_action('wp_ajax_nopriv_filterSpeakers', 'filterSpeakers');




add_shortcode('get_session_speakers_link2025','get_session_speakers_link2025');
function get_session_speakers_link2025() {
	
    if (!empty($_GET['elementor-preview'] ) || (!empty($_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
		return;	
	}
	
	/**  Guarantee the return_value variable always exists  */
	$return_value = '';
    $session_id =  get_the_ID();
	$term_ids = wp_get_post_terms($session_id,'speaking-session-category',array( 'fields' => 'ids' ));
	$speaker_id = get_post_meta($session_id,'cwp_field_227719618722',true);
  $cohost_id = get_post_meta($session_id,'cwp_field_227719618722co',true);

  $workshop_ids = get_post_meta($session_id,'cwp_field_227719618722co',true);


	if(!empty($speaker_id)){
		$speaker_name = get_the_title($speaker_id);
		$speaker_link = get_permalink($speaker_id);
                $speaker_img = get_the_post_thumbnail_url($speaker_id);


    $cohost_name = get_the_title($cohost_id);
		$cohost_link = get_permalink($cohost_id);
                $cohost_img = get_the_post_thumbnail_url($cohost_id);            
		
		// if($term_ids[0] == '57'){
		// 	$return_value .= "<p>Workshop Presenter: </p>";
		// }
		$speaker_company = get_post_meta($speaker_id,'cwp_field_933137458361',true);
    $cohost_company = get_post_meta($cohost_id,'cwp_field_933137458361',true);

		if(!empty($speaker_name)){
      if($term_ids[0] == '57') {
        $return_value .= "<div style='font-size:16px;'>Workshop Presenter: </div>";
      } else {
        $return_value .= "<div style='font-size:16px;'>Speaker: </div>";
      }

			$return_value .= "<a href='$speaker_link' class='card-member' style='color:#262528;'><img style='border-radius:50%;height:56px;width:56px;object-fit:cover;' src='$speaker_img'><div><strong>$speaker_name</strong><br>$speaker_company</div></a>";
		}
	}


  	if(!empty($workshop_ids ) && $term_ids[0] == '57'){
		$total = count($workshop_ids);
		$return_value .= "<div class='panelists-wrapper'>";
		$return_value .= "<span style='font-size:16px;'>Workshop Presenters: </span>";
    $return_value .= "<div class='grid2'>";
		$count = 0;
		foreach($workshop_ids as $workshop_id){
			$workshop_name = get_the_title($workshop_id);
      $workshop_img = get_the_post_thumbnail_url($workshop_id);
			$workshop_link = get_permalink($workshop_id);
			$workshop_company = get_post_meta($workshop_id ,'cwp_field_933137458361',true);
			if(!empty($workshop_img)){
				$return_value .= "<a href='$workshop_link' class='card-member'><img style='border-radius:50%;height:56px;width:56px;object-fit:cover;' src='$workshop_img'><div><strong>$workshop_name</strong><br> $workshop_company</div></a>";
			}
			else {
				// $return_value .= "<a href='$workshop_link'><strong>$workshop_name</strong></a>";
			}
			if(++$count === $total) {
				$return_value .= "</div> </div> <div style='margin-top:10px;'>";
			}
			else{
				// $return_value .= "<span> | </span>";
			}
		}
		// if($moderator_id){
		// 	$moderator_name = get_the_title($moderator_id);
    //   $moderator_img = get_the_post_thumbnail_url($moderator_id);
		// 	$moderator_link = get_permalink($moderator_id);
		// 	$moderator_company = get_post_meta($moderator_id,'cwp_field_933137458361',true);
		// 	$return_value .= "<span style='font-size:16px;'>Moderator: </span>";
    //   $return_value .= "<div class='grid2'>";
		// 	if(!empty($moderator_company)){
		// 		$return_value .= "<a href='$moderator_link' class='card-member'><img style='border-radius:50%;height:56px;width:56px;object-fit:cover;' src='$moderator_img'><div><strong>$moderator_name</strong><br> $moderator_company</div></a>";
		// 	}
    //   $return_value .= "</div>";
		// }
   
		$return_value .= "</div>";
	}

	$panelist_ids = get_post_meta($session_id,'cwp_field_261211398069',true);
	$moderator_id = get_post_meta($session_id,'cwp_field_480325353017',true);
	if(is_array($panelist_ids)){
		$panelist_ids = array_filter($panelist_ids);
	}
	if(!empty($panelist_ids) || !empty($moderator_id)){
    if(!empty($panelist_ids)){
		$total = count($panelist_ids);
		$return_value .= "<div class='panelists-wrapper'>";
    $speakers_title = 'Panelists:';
    if($term_ids[0] == '105') {
       $speakers_title = 'Speakers';
    }
		$return_value .= "<span style='font-size:16px;'>" . $speakers_title .  "</span>";
    $return_value .= "<div class='grid2'>";
		$count = 0;
		foreach($panelist_ids as $panelist_id){
			$panelist_name = get_the_title($panelist_id);
      $panelist_img = get_the_post_thumbnail_url($panelist_id);
			$panelist_link = get_permalink($panelist_id);
			$panelist_company = get_post_meta($panelist_id,'cwp_field_933137458361',true);
			if(!empty($panelist_company)){
				$return_value .= "<a href='$panelist_link' class='card-member'><img style='border-radius:50%;height:56px;width:56px;object-fit:cover;' src='$panelist_img'><div><strong>$panelist_name</strong><br> $panelist_company</div></a>";
			}
			else{
				$return_value .= "<a href='$panelist_link' class='card-member'><strong>$panelist_name</strong></a>";
			}
			if(++$count === $total) {
				$return_value .= "</div> </div>";
			}
			else{
				// $return_value .= "<span> | </span>";
			}
		}
    }
    $return_value .= "<div style='margin-top:10px;'>";
			
		if($moderator_id){
			$moderator_name = get_the_title($moderator_id);
      $moderator_img = get_the_post_thumbnail_url($moderator_id);
			$moderator_link = get_permalink($moderator_id);
			$moderator_company = get_post_meta($moderator_id,'cwp_field_933137458361',true);
			$return_value .= "<span style='font-size:16px;'>Moderator: </span>";
      $return_value .= "<div class='grid2'>";
			if(!empty($moderator_company)){
				$return_value .= "<a href='$moderator_link' class='card-member'><img style='border-radius:50%;height:56px;width:56px;object-fit:cover;' src='$moderator_img'><div><strong>$moderator_name</strong><br> $moderator_company</div></a>";
			}
      $return_value .= "</div>";
		}
   
		$return_value .= "</div>";
	}

  $return_value .= "<style> .grid3 {display:grid;gap:10px;} .card-member{color:#262528!important;display:flex;align-items:center;gap:10px;line-height:1.2;}</style>";
  $return_value .= "<style> .grid2 {display:grid;grid-template-columns:1fr 1fr;gap: 10px;} .agenda-type-panel </style>";
  $return_value .= "<style> .session-loop-zoom-link a {color: #262528;
    font-family: var(--e-global-typography-d02a67e-font-family), Sans-serif;
    font-size: var(--e-global-typography-d02a67e-font-size);
    font-weight: var(--e-global-typography-d02a67e-font-weight);
    line-height: var(--e-global-typography-d02a67e-line-height);
    letter-spacing: var(--e-global-typography-d02a67e-letter-spacing);
    fill: #06010F;
    color: #06010F;
    border-style: solid;
    border-width: 2px 2px 2px 2px;
    border-radius: 4px 4px 4px 4px;
    padding: 12px 12px 12px 12px;} .session-loop-zoom-link {line-height:0;display:flex;align-items: flex-end;} .session-loop-button{display:flex;align-items:flex-end;}
    .speaking-session-category-fireside-chat .panelists-wrapper .grid2 {grid-template-columns:1fr!important;}
    </style>";
  $return_value .= "<style>@media screen and (min-width:768px){}</style>";
    $return_value .= "<style>@media screen and (min-width:1024px){.grid3 {grid-template-columns:repeat(3,1fr);} .agenda-type-panel .cols-adjust {grid-template-columns:3fr 1fr!important;}}</style>";
  return $return_value;
}


add_shortcode('hide_zoom_qa_link2025','hide_zoom_qa_link2025');
function hide_zoom_qa_link2025() {
    if (!empty($_GET['elementor-preview']))
		return;
 	$session_id =  get_the_ID();
	$zoom_link = get_post_meta($session_id,'cwp_field_15581632866',true);


	if(empty($zoom_link)){
		return '<script>
			jQuery("#'.$session_id.'.session-loop-zoom-link").hide();
		</script>';
	}
	$qa_link = get_post_meta($session_id,'cwp_field_394869217799',true);
	if(empty($qa_link)){
		return '<script>
			jQuery("#'.$session_id.'.session-loop-qa-link").hide();
		</script>';
	}
}




// filter Agenda 2005 v.2
add_shortcode('filter_agenda','filter_agenda');
function filter_agenda() {
	if (!empty($_GET['elementor-preview'])) return;

	printf('<script>var ajaxurl = "%s";</script>', esc_url(admin_url('admin-ajax.php')));

	// ─────────────────────────────────────────────────────────────────────────────
	// Helpers
	// ─────────────────────────────────────────────────────────────────────────────

  /* normalize sessions to a clean array of valid post IDs */
	if (!function_exists('normalize_sessions')) {
		function normalize_sessions($raw){
			if (empty($raw)) return [];
			$sessions = [];

			// ACF relationship can be: CSV string, list of IDs, list of arrays/objects with id
			if (is_string($raw)) {
				// split on comma or whitespace
				$parts = preg_split('/[,\s]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($parts as $p) { $sessions[] = (int)$p; }
			} elseif (is_array($raw)) {
				foreach ($raw as $row) {
					if (is_array($row) && isset($row['id']))        $sessions[] = (int)$row['id'];
					elseif (is_object($row) && isset($row->id))     $sessions[] = (int)$row->id;
					else                                            $sessions[] = (int)$row;
				}
			} else {
				$sessions = [(int)$raw];
			}

			// keep valid, existing posts only
			$sessions = array_values(array_unique(array_filter($sessions, function($id){
				return $id > 0 && get_post_status($id);
			})));

			return $sessions;
		}
	}

  /* normalize the content-type field to slug keys, label, and columns */
	if (!function_exists('normalize_content_type')) {
		function normalize_content_type($raw) {
			$keys  = [];
			$label = 'Other';

			$push_term = function($term) use (&$keys, &$label) {
				if ($term && !is_wp_error($term)) {
					$slug = sanitize_title($term->slug);
					$name = $term->name;
					$keys[] = $slug;
					if ($label === 'Other' && !empty($name)) $label = $name;
				}
			};

			if (is_array($raw)) {
				foreach ($raw as $item) {
					if (is_numeric($item))                                $push_term(get_term((int)$item, 'speaking-session-category'));
					elseif (is_object($item) && isset($item->term_id))     $push_term($item);
					elseif (is_string($item) && $item !== '') {
						$t = get_term_by('slug', sanitize_title($item), 'speaking-session-category');
						if (!$t && is_numeric($item)) $t = get_term((int)$item, 'speaking-session-category');
						$push_term($t);
					}
				}
			} else {
				if (is_numeric($raw))                                $push_term(get_term((int)$raw, 'speaking-session-category'));
				elseif (is_object($raw) && isset($raw->term_id))     $push_term($raw);
				elseif (is_string($raw) && $raw !== '') {
					$t = get_term_by('slug', sanitize_title($raw), 'speaking-session-category');
					if (!$t && is_numeric($raw)) $t = get_term((int)$raw, 'speaking-session-category');
					$push_term($t);
				}
			}

			if (empty($keys)) { $keys = ['other']; $label = 'Other'; }

			// collapse to badge key & column count
			$badge_key_map = [
				'panel-discussion'      => 'panel',
				'panel_discussion'      => 'panel',
				'wide-panel-discussion' => 'panel',
				'wide_panel_discussion' => 'panel',
				'speaker-session'       => 'speaking_session',
  				'speaker_session'       => 'speaking_session',
                                'keynote'               => 'keynote',
                                'fireside_chat'         => 'fireside-chat',
                                'workshop'              => 'workshop',
                                'unconference'          => 'unconference',
                                'vip-session'           => 'vip-session',
                                'pitch'                 => 'pitch',
                                'demo'                  =>  'demo'
			];
			$primary = $keys[0];
			$badge   = $badge_key_map[$primary] ?? $primary;
			if (!in_array($badge, ['panel','speaking_session','breakout-speaking','other','keynote','workshop','unconference','pitch','fireside-chat','vip-session','demo' ], true)) $badge = 'other';

			$panelish = ['panel','panel-discussion','panel_discussion','wide-panel-discussion','wide_panel_discussion', 'workshop', 'vip-session','fireside_chat','fireside-chat','pitch', 'unconference', 'demo'];
			$has_panelish = (bool) array_intersect($panelish, $keys);
			$cols = $has_panelish ? 1 : 2;

			return [
				'keys'  => $keys,
				'label' => $label,
				'badge' => $badge,
				'cols'  => $cols,
			];
		}
	}

	// Accumulators for filter options
	$opt_categories = [];   // speaking-session-category (term_id => name)
	$opt_tracks     = [];   // ai-tracks (term_id => name)
	$opt_themes     = [];   // speaking-session-tag (term_id => name)
	$opt_locations  = [];   // location label => label

	// Query agenda posts
	$agenda_args = [
		'post_type'      => 'agenda',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'tax_query' => [
			[
				'taxonomy' => 'agenda-type',
				'field'    => 'term_id',
				'terms'    => 85,
			],
		],
	];
	$agenda_q = new WP_Query($agenda_args);

	// Parent "fall-2025" term for day radios
	$fall_parent = get_term_by('slug', 'fall-2025', 'agenda-type');
	$day_terms   = [];
	if ($fall_parent && ! is_wp_error($fall_parent)) {
		$day_terms = get_terms([
			'taxonomy'   => 'agenda-type',
			'hide_empty' => false,
			'parent'     => $fall_parent->term_id,
			'orderby'    => 'term_order',
			'order'      => 'ASC',
		]);
	}

	$agenda_html = '';

	// ─────────────────────────────────────────────────────────────────────────────
	// Loop agenda posts
	// ─────────────────────────────────────────────────────────────────────────────
	if ($agenda_q->have_posts()) {
		while ($agenda_q->have_posts()) {
			$agenda_q->the_post();
			$agenda_id  = get_the_ID();
			$event_time = get_post_meta($agenda_id, 'cwp_field_1095651109', true);

			// Find the child Day term
			$block_day_id = '';
			if ($fall_parent && ! is_wp_error($fall_parent)) {
				$agenda_terms = wp_get_post_terms($agenda_id, 'agenda-type', ['fields' => 'all']);
				if (!is_wp_error($agenda_terms)) {
					foreach ($agenda_terms as $at) {
						if ((int)$at->parent === (int)$fall_parent->term_id) { $block_day_id = (string)$at->term_id; break; }
					}
				}
			}

			// Field pairs (primary + secondary)
			$ct1_raw       = get_post_meta($agenda_id, 'cwp_field_2002819209', true);
			$sessions1_raw = get_post_meta($agenda_id, 'cwp_field_1499880473', true);

			$ct2_raw       = get_post_meta($agenda_id, 'cwp_field_20028192t2', true);    
			$sessions2_raw = get_post_meta($agenda_id, 'cwp_field_14998804t2', true);     

			// normalize BOTH session lists before testing emptiness
			$sessions1 = normalize_sessions($sessions1_raw);
			$sessions2 = normalize_sessions($sessions2_raw);

			$norm1 = normalize_content_type($ct1_raw);
			$norm2 = normalize_content_type($ct2_raw);

			$has_any_sessions = !empty($sessions1) || !empty($sessions2);


      // count non-empty sets
      $non_empty_sets = 0;
      if (!empty($sessions1)) $non_empty_sets++;
      if (!empty($sessions2)) $non_empty_sets++;

			if ($has_any_sessions) {
				/* print time ONCE for the first printed block only */
				$printed_any_block = false;

				$sets = [
					['norm'=>$norm1, 'sessions'=>$sessions1],
					['norm'=>$norm2, 'sessions'=>$sessions2],
				];



				foreach ($sets as $idx => $set) {
					if (empty($set['sessions'])) continue;

					$block_classes = 'agenda-block';
					if ($printed_any_block) $block_classes .= ' agenda-subblock'; // second+ block styling

           // add single-ct class if only one non-empty set
          if ($non_empty_sets === 1) {
              $block_classes .= ' single-ct';
          }


					ob_start();
					printf(
						'<section class="%s agenda-type-%s" data-content-type="%s" data-day="%s">',
						esc_attr($block_classes),
						esc_attr($set['norm']['badge']),
						esc_attr(implode(' ', $set['norm']['keys'])),
						esc_attr($block_day_id)
					);


					if (!empty($event_time)) {
						echo '<div class="agenda-head"><div class="agenda-time" style="margin:0 0 10px;">'
							 . '<small style="font-weight:700;font-size:20px;">' . esc_html($event_time) . '</small>'
							 . '</div></div>';
					}

					printf('<div class="sessions-container" data-content-type="%s">', esc_attr(implode(' ', $set['norm']['keys'])));
					printf('<div class="agenda-type-badge badge-%s">%s</div>', esc_attr($set['norm']['badge']), esc_html($set['norm']['label']));
					printf('<div class="sessions-wrapper" style="display:grid;grid-template-columns:repeat(%d,1fr);gap:24px">', (int)$set['norm']['cols']);

					foreach ($set['sessions'] as $sid) {
						$session_post = get_post((int)$sid);
						if (!$session_post) continue;
  if (get_post_status($sid) !== 'publish') continue;

						// Terms + location
						$terms_cat   = wp_get_post_terms($sid, 'speaking-session-category');
						$terms_track = wp_get_post_terms($sid, 'ai-tracks');
						$terms_theme = wp_get_post_terms($sid, 'speaking-session-tag');
						$location_val = get_post_meta($sid, 'cwp_field_947243527337', true);

						foreach ($terms_cat as $t)   { $opt_categories[$t->term_id] = $t->name; }
						foreach ($terms_track as $t) { $opt_tracks[$t->term_id]     = $t->name; }
						foreach ($terms_theme as $t) { $opt_themes[$t->term_id]     = $t->name; }
						if (!empty($location_val))    { $opt_locations[$location_val] = $location_val; }

						$attr_cat   = implode(' ', wp_list_pluck($terms_cat, 'term_id'));
						$attr_track = implode(' ', wp_list_pluck($terms_track, 'term_id'));
						$attr_theme = implode(' ', wp_list_pluck($terms_theme, 'term_id'));

						printf(
							'<div class="session-wrapper" data-category="%s" data-track="%s" data-theme="%s" data-location="%s">',
							esc_attr($attr_cat), esc_attr($attr_track), esc_attr($attr_theme), esc_attr($location_val)
						);

						// Elementor card
						$prev_post = $GLOBALS['post'] ?? null;
						$GLOBALS['post'] = $session_post;
						setup_postdata($GLOBALS['post']);
						echo do_shortcode('[elementor-template id="12412"]');
						if ($prev_post) { $GLOBALS['post'] = $prev_post; setup_postdata($GLOBALS['post']); }
						else { wp_reset_postdata(); }

						echo '</div>'; // .session-wrapper
					}

					echo '</div></div>'; // .sessions-wrapper + .sessions-container
					echo '</section>';

					$agenda_html .= ob_get_clean();
					$printed_any_block = true;
				}
			} else {
				/* Only one fallback block (Other) WITH agenda title, no template */
				ob_start();
				printf(
					'<section class="agenda-block agenda-type-other" data-content-type="other" data-day="%s">',
					esc_attr($block_day_id)
				);

				if (!empty($event_time)) {
					echo '<div class="agenda-head"><div class="agenda-time" style="margin:0 0 10px;">'
						 . '<small style="font-weight:700;font-size:20px;">' . esc_html($event_time) . '</small>'
						 . '</div></div>';
				}

				echo '<div class="sessions-container nofilter" data-content-type="other">';
				// Only agenda TITLE (no session card template)
				echo '<div class="session-empty"><h4 class="elementor-heading-title elementor-size-default">'
					 . esc_html(get_the_title($agenda_id))
					 . '</h4></div>';
				echo '</div>';

				echo '</section>';
				$agenda_html .= ob_get_clean();
			}
		}
		wp_reset_postdata();
	} else {
		$agenda_html = '<p>No agenda items found.</p>';
	}

	// Build server-side options
	$categories_options = '<option value="">All</option>';
	foreach ($opt_categories as $id => $name) $categories_options .= sprintf('<option value="%d">%s</option>', (int)$id, esc_html($name));
	$tracks_options = '<option value="">All</option>';
	foreach ($opt_tracks as $id => $name) $tracks_options .= sprintf('<option value="%d">%s</option>', (int)$id, esc_html($name));
	$themes_options = '<option value="">All</option>';
	foreach ($opt_themes as $id => $name) $themes_options .= sprintf('<option value="%d">%s</option>', (int)$id, esc_html($name));
	$locations_options = '<option value="">All</option>';
	foreach ($opt_locations as $val => $label) $locations_options .= sprintf('<option value="%s">%s</option>', esc_attr($val), esc_html($label));

	// UI + agenda HTML
	ob_start(); ?>

	<div id="session-filter">
		<div class="agenda-days" style="display:flex;gap:16px;flex-wrap:wrap;justify-content:center;">
		<?php if (!empty($day_terms) && ! is_wp_error($day_terms)) : ?>
			<?php $first = true; foreach ($day_terms as $t): ?>
				<label style="display:flex;align-items:center;cursor:pointer;">
					<input type="radio" name="agenda_day_term"
								 value="<?php echo esc_attr($t->term_id); ?>"
								 />
					<h4 style="margin:0;"><?php echo esc_html($t->name); ?></h4>
				</label>
			<?php $first = false; endforeach; ?>
		<?php endif; ?>
		</div>

		<div class="filter-wrapper">
			<div class="filter-item-search">
				<label>Search</label>
				<input type="text" name="filter_keyword" placeholder="Search" />
			</div>

			<div class="m-row">
				<div>
					<label>AI Track</label>
					<select name="filter_track"><?php echo $tracks_options; ?></select>
				</div>
				<div>
					<label>AI Theme</label>
					<select name="filter_theme"><?php echo $themes_options; ?></select>
				</div>
        <div>
					<label>Content Type</label>
					<select name="filter_category"><?php echo $categories_options; ?></select>
				</div>
				<!--
				<div>
					<label>Location</label>
					<select name="filter_location"><?php //echo $locations_options; ?></select>
				</div>
				-->
			</div>
			<img class="loading-image" src="<?php echo HELLO_CHILD_WP_UPLOADS; ?>/2024/09/loading-greybg.gif" style="display:none;height:28px" />
		</div>

		<?php
		// Predefined locations, 'San Francisco' removed
		$location_radios = ['All', '1st Floor Event Center', '2nd Floor Event Center', '2nd Floor Silicon Valley'];
		?>
		<div class="agenda-locations">
			<?php foreach ($location_radios as $i => $loc_label): ?>
				<label style="cursor:pointer;">
					<input type="radio" name="agenda_location" value="<?php echo esc_attr($loc_label); ?>" <?php echo checked($i === 0, true, false); ?> />
					<span><?php echo esc_html($loc_label); ?></span>
				</label>
			<?php endforeach; ?>
		</div>

		<div id="session-result"><?php echo $agenda_html; ?></div>
	</div>

	<style>
		.agenda-type-badge { padding:8px 10px; border-radius:5px; font-size:16px; font-weight:700; display:inline-block; margin:0 0 24px;font-family: var(--e-global-typography-text-font-family),"IBM Plex Sans",Sans-serif; }
		.badge-panel{ background:#E36060; color:#fff; }
		.badge-speaking_session, .badge-breakout-speaking{ background:#8356D4; color:#fff; }
		.badge-fireside_chat, .badge-fireside-chat { background:#F5BF6D; color:#262528; }
		.badge-workshop{ background:#82BBED; color:#262528; }
		.badge-other{ background:#DBDADC; color:#333; }
                .badge-keynote {background:#D6C7F1; color:#262528}
    .badge-speedgeeking { background:#D6C7F1; color:#262528; }   
    .badge-demo { background:#F5BF6D; color:#262528; }          
    .badge-unconference, .badge-pitch {
      background: #74D1D1;color:#262528;
    }
    .badge-vip-session {
      background:#555357;color:#fff;
    }

		.agenda-block { padding:40px 0;}
    /*.agenda-block.single-ct, .agenda-block.agenda-subblock, .agenda-block.agenda-type-other 
{border-bottom:1px solid #8F8D91;}*/
.agenda-block.single-ct, .agenda-block.agenda-subblock {border-bottom:1px solid #8F8D91;}
.agenda-block.agenda-type-other[data-content-type="other"] {border-bottom:1px solid #8F8D91;}
		.agenda-subblock { padding:0; margin-top:0; }
    .agenda-subblock .agenda-time {opacity: 0;}

		.agenda-head { text-align:center; }
    .agenda-head .agenda-time {font-family: var(--e-global-typography-text-font-family),"IBM Plex Sans",Sans-serif;}
		.select2-container .select2-dropdown { top: 32px!important; }

		.agenda-locations { display: grid; grid-template-columns: repeat(4, 1fr); text-align: center; align-items: flex-end; }
		#session-filter { padding: 0 20px; overflow: hidden; }
		#session-filter .agenda-locations .active { font-weight: bold; border-bottom: 2px solid #262528; }
		.session-wrapper .industry-item { display:inline-block; background:#DBDADC; border-radius:4px; padding:4px 8px; margin-left:5px; }
		.filter-wrapper .select2 span, .select2-results li {font-family: var(--e-global-typography-text-font-family), Sans-serif;}

		#session-filter .filter-wrapper { display:grid; grid-template-columns: 1fr 3fr; gap:26px; margin-bottom:46px; }
		#session-filter .m-row { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
		.agenda-days { margin-bottom:45px; display:flex; }
		#session-filter .agenda-days input[type="radio"], #session-filter .agenda-locations input[type="radio"] { appearance:none; }
    #session-filter .agenda-locations span {display:block;text-align:center;}
    #session-filter .agenda-days h4 {font-family: Lora;}
		.select2-container--default .select2-selection--single .select2-selection__arrow b { display:none; }
		.agenda-days h4 { font-style: italic; font-weight: 500;letter-spacing: -0.02em;
   }
    .agenda-days .active h4 { font-weight: 500;text-decoration: underline;
    text-decoration-thickness:0.5px;text-underline-offset:12px; }
		.selection .select2-selection { height:44px; }
		.select2-container--default .select2-selection--single { border-radius:4px; border:1px solid #262528; background-color:transparent; }
		.select2-container--default .select2-selection--single .select2-selection__arrow {
			height:42px; width:35px;
			background-image:url(<?php echo HELLO_CHILD_THEME_IMAGES_URL; ?>/icon-chevron-down.svg);
			background-size:22px; background-position:center center; background-repeat:no-repeat;
		}
		#session-filter input[type=text] {
			padding:13px 15px; border:1px solid #262528; border-radius:4px; background:transparent; height:44px; width:100%;
			background-repeat:no-repeat; background-position:right 10px center;
			font-size:16px; color:#262528; min-width:200px;
			background-image:url(<?php echo HELLO_CHILD_THEME_IMAGES_URL; ?>/icon-search.svg);
      font-family: var(--e-global-typography-text-font-family), Sans-serif;
		}
    #session-filter .filter-wrapper input[type=text]:placeholder {font-family: var(--e-global-typography-text-font-family), Sans-serif;}
		#session-filter .filter-wrapper select {
			appearance:none; max-width:100%; min-width:280px; padding:13px 24px; border:1px solid #262528; border-radius:4px; background:transparent;
			background-image:url(<?php echo HELLO_CHILD_WP_UPLOADS; ?>/2024/09/AISummit-Select_without_border.svg);
			background-repeat:no-repeat; background-size:32px; background-position:right 20px center;
			color:#908F91; font-family:var(--e-global-typography-text-font-family), Sans-serif !important;
			font-size:var(--e-global-typography-text-font-size) !important; font-weight:var(--e-global-typography-text-font-weight) !important;
		}
		#session-filter label { color:var(--e-global-color-text); margin-bottom:5px; }
		#session-filter .filter-wrapper label { font-weight:700; }
		#session-filter .agenda-locations label { border-bottom:1px solid #262528; padding:8px 3px;font-family: var(--e-global-typography-text-font-family),"IBM Plex Sans",Sans-serif;text-align:center; }
    .session-empty > h4 {font-family: var(--e-global-typography-text-font-family),"IBM Plex Sans",Sans-serif;}
   
		@media screen and (min-width:768px) {
			#session-filter .agenda-days h4 { font-size:32px; margin:0 20px; }

      .session-wrapper .ss-loop2025-p2 { height:100%; justify-content:space-between; }
		}
		@media (min-width:1024px){
			.agenda-block { display:grid; gap:10px; grid-template-columns:1fr 5fr; }
			.agenda-locations { grid-template-columns: repeat(4, 1fr); gap: 0 24px; padding-left:200px; margin-bottom:20px; }
      #session-filter .m-row { gap:26px; }
		}


		@media (max-width:767px){ 
      #session-filter .agenda-days {gap:8px!important;}
      #session-filter .agenda-days h4 { font-size:20px; }
      .sessions-wrapper { grid-template-columns: 1fr!important; } 
      .ss-loop2025-ind-wrapper {text-align: left!important; }
      .ss-loop2025-ind-wrapper .industry-item:first-of-type {margin-left: 0!important; }
    }
		@media (max-width:1024px){
			#session-filter .filter-item-search { grid-column: 1 / -1; }
			#session-filter .filter-wrapper { grid-template-columns: 1fr 3fr; margin-bottom: 0; }
			#session-filter .m-row { grid-template-columns: repeat(3, minmax(0,1fr)); grid-template-rows:auto auto; grid-column:1/3; }
			#session-filter .select2-container { max-width:100%; }
		}
	</style>

	<script>
	(function($){
	  let isResetting    = false;
	  let currentFilter  = null;   // 'filter_category' | 'filter_track' | 'filter_theme' | 'filter_location' | 'filter_keyword' | null
	  let currentDay     = null;   // agenda day term_id (string)
	  let currentLocRad  = 'All';  // default to All
	  let currentKeyword = '';     // search text (lowercased)

	  function setSelect($sel, val){
	    if ($sel.hasClass('select2-hidden-accessible')) { $sel.val(val).trigger('change.select2'); }
	    else { $sel.val(val); }
	  }

	  function resetOthersExcept(nameKeep){
	    const names = ['filter_category','filter_track','filter_theme','filter_location'];
	    isResetting = true;
	    names.forEach(n => { if (n !== nameKeep) setSelect($('select[name="'+n+'"]'), ''); });
	    isResetting = false;
	  }

	  function resetAllSelects(){ resetOthersExcept('__none__'); }

	  function clearKeyword(){
	    const $kw = $('input[name="filter_keyword"]'); $kw.val(''); currentKeyword = '';
	  }

	  function getAttr($el, key){ return String($el.attr('data-' + key) || '').trim(); }
	  function parseIdList(raw){ if (!raw) return []; return raw.replace(/,/g,' ').split(/\s+/).map(s => s.trim()).filter(Boolean); }
	  function debounce(fn, wait){ let t=null; return function(){ const a=arguments,c=this; clearTimeout(t); t=setTimeout(function(){ fn.apply(c,a); }, wait); }; }
	  function normLoc(s){ return String(s||'').toLowerCase().replace(/\s+/g,' ').trim(); }

	  function applyFilter(){
	    // Baseline: show everything
	    $('.session-wrapper').show();
	    $('.agenda-block').show();

	    // Day filter (AND)
	    if (currentDay && currentDay !== '') {
	      $('.agenda-block').each(function(){
	        if (String(getAttr($(this), 'day')) !== String(currentDay)) $(this).hide();
	      });
	    }

	    // Location radio (AND)
	    if (currentLocRad && normLoc(currentLocRad) !== 'all') {
	      const want = normLoc(currentLocRad);
	      $('.agenda-block:visible').each(function(){
	        const $block = $(this);
	        const isNoFilterBlock = $block.find('.sessions-container.nofilter').length > 0 || $block.hasClass('agenda-type-other');
	        if (isNoFilterBlock) { $block.hide(); return; }

	        let hasMatch = false;
	        $block.find('.session-wrapper').each(function(){
	          const cardLoc = normLoc($(this).attr('data-location'));
	          if (cardLoc === want) { $(this).show(); hasMatch = true; } else { $(this).hide(); }
	        });
	        if (!hasMatch) $block.hide();
	      });
	    }

	    // Single-active dropdown/keyword
	    const selCat   = $('select[name="filter_category"]').val();
	    const selTrack = $('select[name="filter_track"]').val();
	    const selTheme = $('select[name="filter_theme"]').val();
	    const selLoc   = $('select[name="filter_location"]').val();

	    let name=null,val=null, usingKeyword=false;
	    if (currentFilter === 'filter_keyword' && currentKeyword.trim() !== '') usingKeyword = true;
	    else if (currentFilter === 'filter_category'  && selCat   !== '') { name='category';  val=String(selCat).trim(); }
	    else if (currentFilter === 'filter_track'     && selTrack !== '') { name='track';     val=String(selTrack).trim(); }
	    else if (currentFilter === 'filter_theme'     && selTheme !== '') { name='theme';     val=String(selTheme).trim(); }
	    else if (currentFilter === 'filter_location'  && selLoc   !== '') { name='location';  val=String(selLoc).trim(); }

	    if (usingKeyword) {
	      const kw = currentKeyword.toLowerCase();
	      $('.agenda-block:visible').each(function(){
	        const $block = $(this);
	        const isNoFilterBlock = $block.find('.sessions-container.nofilter').length > 0 || $block.hasClass('agenda-type-other');
	        if (isNoFilterBlock) {
	          const blockTxt = $block.text().toLowerCase();
	          const blockHit = blockTxt.indexOf(kw) !== -1;
	          $block.toggle(blockHit);
	          return;
	        }
	        $block.find('.session-wrapper:visible').each(function(){
	          const hit = $(this).text().toLowerCase().indexOf(kw) !== -1;
	          if (!hit) $(this).hide();
	        });
	      });
	      // hide any block that ended with 0 visible sessions (except nofilter)
	      $('.agenda-block:visible').each(function(){
	        const $b = $(this);
	        const isNF = $b.find('.sessions-container.nofilter').length > 0 || $b.hasClass('agenda-type-other');
	        const any  = $b.find('.session-wrapper:visible').length > 0;
	        if (!isNF) $b.toggle(any);
	      });
	      return;
	    }

	    if (name && val) {
	      $('.agenda-block:visible').each(function(){
	        const $block = $(this);
	        const isNoFilterBlock = $block.find('.sessions-container.nofilter').length > 0 || $block.hasClass('agenda-type-other');
	        if (isNoFilterBlock) { $block.hide(); return; }

	        $block.find('.session-wrapper:visible').each(function(){
	          let match=false;
	          if (name === 'location') match = (getAttr($(this),'location').toLowerCase() === val.toLowerCase());
	          else match = parseIdList(getAttr($(this), name)).includes(val);
	          if (!match) $(this).hide();
	        });
	      });
	    }

	    // Hide blocks with 0 visible sessions (keep "other"/nofilter by day only)
	    $('.agenda-block:visible').each(function(){
	      const $block = $(this);
	      const isNoFilterBlock = $block.find('.sessions-container.nofilter').length > 0 || $block.hasClass('agenda-type-other');
	      const any = $block.find('.session-wrapper:visible').length > 0;
	      $block.toggle(isNoFilterBlock ? true : any);
	    });
	  }

	  $(function(){
	    if ($.fn.select2) $('#session-filter select').select2({ width:'resolve', minimumResultsForSearch: Infinity });

	    // Day radios
	    $('input[name="agenda_day_term"]').on('change', function(){
	      $('input[name="agenda_day_term"]').closest('label').removeClass('active');
	      $(this).closest('label').addClass('active');
	      currentDay = $(this).val() || null;
	      applyFilter();
	    });

	    // Location radios
	    $('input[name="agenda_location"]').on('change', function(){
	      $('input[name="agenda_location"]').closest('label').removeClass('active');
	      $(this).closest('label').addClass('active');
	      currentLocRad = $(this).val() || 'All';
	      applyFilter();
	    });

	    // Single-active dropdowns
	    function bindSingleActive(selector, key){
	      $(selector).on('change select2:select', function(){
	        if (isResetting) return;
	        currentFilter = key;
	        clearKeyword();
	        resetOthersExcept(key);
	        applyFilter();
	      });
	    }
	    bindSingleActive('select[name="filter_category"]',  'filter_category');
	    bindSingleActive('select[name="filter_track"]',     'filter_track');
	    bindSingleActive('select[name="filter_theme"]',     'filter_theme');
	    bindSingleActive('select[name="filter_location"]',  'filter_location');

	    // Search
	    const onSearchInput = debounce(function(){
	      currentKeyword = String($(this).val() || '').trim();
	      currentFilter  = currentKeyword ? 'filter_keyword' : null;
	      resetAllSelects();
	      applyFilter();
	    }, 250);
	    $('input[name="filter_keyword"]').on('input', onSearchInput);

	    // Initial state (first day checked, "All" location)
	    const $dayInit = $('input[name="agenda_day_term"]:checked').first();
	    currentDay = $dayInit.length ? $dayInit.val() : null;

	    const $locInit = $('input[name="agenda_location"]:checked').first();
	    currentLocRad = $locInit.length ? $locInit.val() : 'All';

	    currentFilter  = null;
	    currentKeyword = '';

	    // set active classes
	    $('input[name="agenda_day_term"]:checked').closest('label').addClass('active');
	    $('input[name="agenda_location"]:checked').closest('label').addClass('active');


	    applyFilter();
	  });


	})(jQuery);

  (function($){
  // Run a function once the radios exist (retries briefly)
  function whenDayRadiosReady(fn, tries=40){
    const $radios = $('input[name="agenda_day_term"]');
    if ($radios.length) { fn($radios); return; }
    if (tries <= 0) return;
    setTimeout(function(){ whenDayRadiosReady(fn, tries-1); }, 50);
  }

  function activateFirstDay() {
    whenDayRadiosReady(function($radios){
      // Always force-reset radios & active class
      $radios.prop('checked', false).closest('label').removeClass('active');

      const $first = $radios.first();
      if (!$first.length) return;

      // Set checked + active, then trigger both click/change to ensure your handlers run
      $first.prop('checked', true).closest('label').addClass('active');
      // Trigger both because some setups bind on click, some on change
      $first.trigger('click').trigger('change');
    });
  }

  // 1) Normal DOM ready
  $(function(){ activateFirstDay(); });

  // 2) Back/Forward cache restore (Safari/Firefox/Chrome)
  window.addEventListener('pageshow', function(e){
    // If the page came from bfcache OR it’s a back/forward navigation, re-apply
    const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
    const isBF = (nav && nav.type === 'back_forward') || e.persisted === true;
    if (isBF) activateFirstDay();
  });

  // 3) As an extra safety net, if the tab becomes visible again (rare edge), enforce once
  document.addEventListener('visibilitychange', function(){
    if (document.visibilityState === 'visible') {
      // Only enforce if nothing is checked (or the active class got lost)
      const $radios = $('input[name="agenda_day_term"]');
      const anyChecked = $radios.is(':checked');
      const anyActive  = $radios.closest('label').filter('.active').length > 0;
      if (!anyChecked || !anyActive) activateFirstDay();
    }
  });
})(jQuery);


(function($){
  function reapplyDropdownsOnBack(){
    const selects = [
      'filter_category',
      'filter_track',
      'filter_theme',
      'filter_location'
    ];

    selects.forEach(function(name){
      const $sel = $('select[name="'+name+'"]');
      if ($sel.length) {
        const val = $sel.val(); // current restored value
        if (val && val !== '') {
          // Trigger change so your existing bindSingleActive() + applyFilter() logic runs
          $sel.trigger('change');
        }
      }
    });
  }

  // Re-run when returning via Back/Forward cache
  window.addEventListener('pageshow', function(e){
    const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
    const isBF = (nav && nav.type === 'back_forward') || e.persisted === true;
    if (isBF) {
      reapplyDropdownsOnBack();
    }
  });

  // Safety: also run shortly after DOM ready (for cases where selects render late)
  $(function(){
    setTimeout(reapplyDropdownsOnBack, 500);
  });
})(jQuery);
(function($){
  function clearAllDropdownsAndSearch(){
    const names = ['filter_category','filter_track','filter_theme','filter_location'];
    names.forEach(function(n){
      const $sel = $('select[name="'+n+'"]');
      if (!$sel.length) return;
      if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.val('').trigger('change.select2');   
      } else {
        $sel.val('').trigger('change');           
      }
    });
    const $kw = $('input[name="filter_keyword"]');
    if ($kw.length){ $kw.val('').trigger('input'); } 
  }

  function resetOnBack(){
    clearAllDropdownsAndSearch();
  }

  $(function(){
    window.addEventListener('pageshow', function(e){
      if (e.persisted || (performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
        resetOnBack();
      }
    });

    const navType = performance.getEntriesByType('navigation')[0]?.type;
    if (navType === 'back_forward') resetOnBack();
  });
})(jQuery);


	</script>

	<?php
	return ob_get_clean();
}



// get taxonomies
// Shortcode: [speaker_content_types]
add_shortcode('speaker_content_types', function($atts){
    global $post;

    if (!is_singular('speaking-session')) {
        return '';
    }

    $taxonomy = 'speaking-session-category';

    $terms = get_the_terms($post->ID, $taxonomy);

    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    $out = '<div class="speaker-content-types">';
    foreach ($terms as $term) {
        $slug = sanitize_title($term->slug);

        // Badge class map
        $badge_key_map = [
            'panel-discussion'      => 'panel',
            'panel_discussion'      => 'panel',
            'wide-panel-discussion' => 'panel',
            'wide_panel_discussion' => 'panel',
            'speaker-session'       => 'speaking_session',
            'speaker_session'       => 'speaking_session',
            'fireside-chat'         => 'fireside_chat',
            'workshop'              => 'workshop',
            'speedgeeking'          => 'speedgeeking',
            'unconference'          => 'unconference',
            'keynote'               => 'keynote',
            'pitch'               => 'pitch',
            'vip-session'         => 'vip-session',
            'demo'                => 'demo'
        ];

        $badge_key = $badge_key_map[$slug] ?? $slug;
        if (!in_array($badge_key, ['panel','speaking_session','keynote','fireside_chat','workshop','speedgeeking','unconference','other','pitch','vip-session', 'demo'], true)) {
            $badge_key = 'other';
        }

        $out .= sprintf(
            '<span class="speaker-ct badge-%s">%s</span>',
            esc_attr($badge_key),
            esc_html($term->name)
        );
    }
    $out .= '</div>';

    // Inline styles
    $out .= '<style>
        .speaker-content-types {
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }
        .speaker-content-types .speaker-ct {
            padding:8px 12px;
            border-radius:5px;
            font-size:16px;
            font-weight:600;
            display:inline-block;
        }
        .badge-panel { background:#E36060; color:#fff; }
        .badge-speaking_session { background:#8356D4; color:#fff; }
        .badge-fireside_chat, .badge-fireside-chat { background:#F5BF6D; color:#262528; }
        .badge-workshop { background:#82BBED; color:#262528; }
        .badge-other { background:#DBDADC; color:#262528; }
        .badge-speedgeeking { background:#D6C7F1; color:#262528; }
        .badge-demo { background:#F5BF6D; color:#262528; }
        .badge-unconference, .badge-pitch { background:#74D1D1; color:#262528; }
        .badge-vip-session {background:#555357; color:#fff}
        .badge-keynote { background:#D6C7F1; color:#262528 }
    </style>';

    return $out;
});



// Track role during Elementor queries
add_action('elementor/query/session-panelist', function($query){
    global $session_speaker_role;
    // $session_speaker_role = 'Panelist';

    $session_id = get_the_ID();

	// Check if this session has term ID 33 in taxonomy 'speaking-session-category'
	if (has_term(105, 'speaking-session-category', $session_id)) {
		$session_speaker_role = 'Speaker';
	} else {
		$session_speaker_role = 'Panelist';
	}
});

add_action('elementor/query/session-moderator', function($query){
    global $session_speaker_role;
    $session_speaker_role = 'Moderator';
});



add_action('elementor/query/session-cohost', function($query){
    global $session_speaker_role;
    $session_speaker_role = 'Workshop Presenter';
});

// Shortcode for Speaker template
add_shortcode('speaker_role_label','get_session_speaker_role');
function get_session_speaker_role() {
    if (!empty($_GET['elementor-preview'])) return '';

    global $session_speaker_role;
    return $session_speaker_role ?? '';
}



// get ai-tracks
add_shortcode('get_session_ai_tracks','get_session_ai_tracks');
function get_session_ai_tracks() {
	
    if (!empty($_GET['elementor-preview']))
		return;
	
	$return_value = '';
  $session_id =  get_the_ID();
	$session_taxonomy = 'ai-tracks';
	$terms_list = wp_get_post_terms($session_id,$session_taxonomy);
	if($terms_list){
    $return_value .= "<div class='ss-top-tracks'>";
		foreach($terms_list as $term){
			$return_value .= "<div class='session-track $term->name'>$term->name</div>";
		}
    $return_value .= "</div>";
	}
	else{
		return;
	}
	
    return $return_value;
}



// get session time
add_shortcode('get_session_time', 'get_session_time');
function get_session_time() {
	if (!empty($_GET['elementor-preview'])) return;

	global $post;
	if (empty($post)) return '';

	$post_id = $post->ID;
	$post_type = get_post_type($post_id);
	$return_value = '';

	// Case 1: Directly on a Session template
	if ($post_type === 'speaking-session') {
		$session_time = get_post_meta($post_id, 'cwp_field_163566829053', true);
		if ($session_time) {
			return '<span class="session-time">' . esc_html($session_time) . '</span>';
		}
		return '';
	}

	// Case 2: On a Speaker template within Elementor "speaker-sessions" query
	if ($post_type === 'speaker') {
		// Elementor already queries related sessions via your filter `speaker_get_related_sessions`.
		// So we just print the time for each session currently in loop.
		$session_time = get_post_meta($post_id, 'cwp_field_163566829053', true);

		// If it's inside a session context (Elementor repeater item), this field exists
		if ($session_time) {
			$return_value .= '<span class="session-time">' . esc_html($session_time) . '</span>';
		}
	}

	return $return_value ? '<div class="speaker-session-times">' . $return_value . '</div>' : '';
}



add_shortcode('get_session_duration', 'get_session_duration');
function get_session_duration() {
	if (!empty($_GET['elementor-preview'])) return;

	global $post;
	if (empty($post)) return '';

	$post_id = $post->ID;
	$post_type = get_post_type($post_id);
	$return_value = '';

	// Case 1: Directly on a Session template
	if ($post_type === 'speaking-session') {
		$session_time = get_post_meta($post_id, 'cwp_field_472229017311', true);
		if ($session_time) {
			return '<span class="session-time">' . esc_html($session_time) . ' min</span>';
		}
		return '';
	}

	// Case 2: On a Speaker template within Elementor "speaker-sessions" query
	if ($post_type === 'speaker') {
		$session_time = get_post_meta($post_id, 'cwp_field_472229017311', true);

		if ($session_time) {
			$return_value .= '<span class="session-time">' . esc_html($session_time) . ' min</span>';
		}
	}

	return $return_value ? '<div class="speaker-session-times">' . $return_value . ' min</div>' : '';
}



// get location
add_shortcode('get_session_location', 'get_session_location');
function get_session_location() {
	if (!empty($_GET['elementor-preview'])) return;

	global $post;
	if (empty($post)) return '';

	$post_id = $post->ID;
	$post_type = get_post_type($post_id);
	$return_value = '';

	// Case 1: Directly on a Session template
	if ($post_type === 'speaking-session') {
		$session_time = get_post_meta($post_id, 'cwp_field_947243527337', true);
		if ($session_time) {
			return '<span class="session-location">' . '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
<g clip-path="url(#clip0_33395_3521)">
<path d="M14 6.66675C14 11.3334 8 15.3334 8 15.3334C8 15.3334 2 11.3334 2 6.66675C2 5.07545 2.63214 3.54933 3.75736 2.42411C4.88258 1.29889 6.4087 0.666748 8 0.666748C9.5913 0.666748 11.1174 1.29889 12.2426 2.42411C13.3679 3.54933 14 5.07545 14 6.66675Z" stroke="#262528" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M8 8.66675C9.10457 8.66675 10 7.77132 10 6.66675C10 5.56218 9.10457 4.66675 8 4.66675C6.89543 4.66675 6 5.56218 6 6.66675C6 7.77132 6.89543 8.66675 8 8.66675Z" stroke="#262528" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_33395_3521">
<rect width="16" height="16" fill="white"/>
</clipPath>
</defs>
</svg> ' . esc_html($session_time) . '</span>';
		}
		return '';
	}

	// Case 2: On a Speaker template within Elementor "speaker-sessions" query
	if ($post_type === 'speaker') {
		$session_time = get_post_meta($post_id, 'cwp_field_947243527337', true);

		if ($session_time) {
			$return_value .= '<span class="session-location">' . '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
<g clip-path="url(#clip0_33395_3521)">
<path d="M14 6.66675C14 11.3334 8 15.3334 8 15.3334C8 15.3334 2 11.3334 2 6.66675C2 5.07545 2.63214 3.54933 3.75736 2.42411C4.88258 1.29889 6.4087 0.666748 8 0.666748C9.5913 0.666748 11.1174 1.29889 12.2426 2.42411C13.3679 3.54933 14 5.07545 14 6.66675Z" stroke="#262528" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M8 8.66675C9.10457 8.66675 10 7.77132 10 6.66675C10 5.56218 9.10457 4.66675 8 4.66675C6.89543 4.66675 6 5.56218 6 6.66675C6 7.77132 6.89543 8.66675 8 8.66675Z" stroke="#262528" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_33395_3521">
<rect width="16" height="16" fill="white"/>
</clipPath>
</defs>
</svg> 888' . esc_html($session_time) . '</span>';
		}
	}

	return $return_value ? '<div class="speaker-session-location">777' . $return_value . '</div>' : '';
}



// Shortcode: [get_session_category_title]
add_shortcode('get_session_category_title', function() {
    if (!empty($_GET['elementor-preview'])) return;

    $session_id = get_the_ID();
    if (!$session_id) return '';

    // Get assigned term IDs from taxonomy
    $term_ids = wp_get_post_terms($session_id, 'speaking-session-category', ['fields' => 'ids']);

    if (empty($term_ids) || !is_array($term_ids)) return '';

    // Define your custom labels
    $custom_labels = [
        48  => 'Panelists', 
        105 => 'Speakers',  // ID 105 → "Demo"
        84  => 'Panelists'
    ];

    foreach ($term_ids as $id) {
        if (isset($custom_labels[$id])) {
            return '<h2 class="session-cat-title">'.$custom_labels[$id].'</h2>';
        }
    }

    // Optional: fallback (if needed)
    return '';
});



/**
 * Shortcode: [speaker_youtube_videos]
 * Reads repeater: cwp_field_234646558501
 * Subfield url: cwp_field_782344176325
 */
add_shortcode('speaker_youtube_videos', function($atts){
  if (!empty($_GET['elementor-preview'])) return '';

  $atts = shortcode_atts([
    'post_id' => 0,          // optional override
    'class'   => '',         // optional extra class on wrapper
  ], $atts);

  $post_id = absint($atts['post_id']) ?: get_the_ID();
  if (!$post_id) return '';

  // Get repeater rows (Crocoblock/CWP style may store as array in post meta)
  $rows = get_post_meta($post_id, 'cwp_field_2346465585', true);
  if (!is_array($rows) || empty($rows)) return '';

  // Extract valid URLs from subfield
  $urls = [];
  foreach ($rows as $row) {
    if (!is_array($row)) continue;
    $u = $row['cwp_field_7823441763'] ?? $row['url'] ?? '';
    $u = trim((string)$u);
    if ($u !== '' && filter_var($u, FILTER_VALIDATE_URL)) $urls[] = $u;
  }

  $urls = array_values(array_unique($urls));
  if (empty($urls)) return '';

  // Convert youtube url to embed URL
  $to_embed = function(string $url): ?string {
    $url = trim($url);
    if ($url === '') return null;

    // If already embed
    if (strpos($url, 'youtube.com/embed/') !== false) return $url;

    $parts = wp_parse_url($url);
    if (empty($parts['host'])) return null;

    $host = preg_replace('/^www\./','', strtolower($parts['host']));

    // youtu.be/<id>
    if ($host === 'youtu.be') {
      $id = trim($parts['path'] ?? '', '/');
      return $id ? 'https://www.youtube.com/embed/' . rawurlencode($id) : null;
    }

    // youtube.com/watch?v=<id>
    if ($host === 'youtube.com' || $host === 'm.youtube.com' || $host === 'music.youtube.com') {
      // /watch
      if (!empty($parts['query'])) {
        parse_str($parts['query'], $q);
        if (!empty($q['v'])) return 'https://www.youtube.com/embed/' . rawurlencode($q['v']);
      }
      // /shorts/<id>
      if (!empty($parts['path']) && preg_match('~^/shorts/([^/?#]+)~', $parts['path'], $m)) {
        return 'https://www.youtube.com/embed/' . rawurlencode($m[1]);
      }
    }

    return null;
  };

  $embeds = [];
  foreach ($urls as $u) {
    $e = $to_embed($u);
    if ($e) $embeds[] = $e;
  }
  if (empty($embeds)) return '';

  $cols = (count($embeds) > 1) ? 2 : 1;
  $wrap_class = 'cwp-youtube-grid cols-' . $cols . ($atts['class'] ? ' ' . sanitize_html_class($atts['class']) : '');

  ob_start();
  ?>
  <div class="<?php echo esc_attr($wrap_class); ?>">
    <?php foreach ($embeds as $src): ?>
      <div class="cwp-youtube-item">
        <div class="cwp-youtube-ratio">
          <iframe
            src="<?php echo esc_url($src); ?>"
            title="YouTube video"
            loading="lazy"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <style>
    .cwp-youtube-grid{display:grid;gap:24px;}
    .cwp-youtube-grid.cols-2{grid-template-columns:repeat(2,minmax(0,1fr));}
    .cwp-youtube-grid.cols-1{grid-template-columns:1fr;}
    @media (max-width: 767px){
      .cwp-youtube-grid.cols-2{grid-template-columns:1fr;}
    }
    .cwp-youtube-ratio{position:relative;width:100%;padding-top:56.25%;overflow:hidden;border-radius:8px;}
    .cwp-youtube-ratio iframe{position:absolute;inset:0;width:100%;height:100%;}
  </style>
  <?php
  return ob_get_clean();
});