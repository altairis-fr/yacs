<?php
/**
 * the cloud of tags
 *
 * @author Bernard Paques
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once 'categories.php';

// load the skin
load_skin('categories');

// the title of the page
$context['page_title'] = i18n::s('The cloud of tags');

// commands for associates
if(Surfer::is_associate())
	$context['page_menu'] = array_merge($context['page_menu'], array( 'categories/edit.php' => i18n::s('Add a category'),
		'categories/check.php' => i18n::s('Maintenance') ));

// the list of active categories
$cache_id = 'categories/cloud.php#content';
if(!$text =& Cache::get($cache_id)) {

	// query the database and layout that stuff
	if(!$text =& Members::list_categories_by_count_for_anchor(NULL, 0, 200, 'cloud'))
		$text = '<p>'.i18n::s('No item has been found.').'</p>';

	// we have an array to format
	if(is_array($text))
		$text =& Skin::build_list($text, '2-columns');

	// make a box
	if($text)
		$text =& Skin::build_box('', $text, 'header1', 'categories');

	// cache this to speed subsequent queries
	Cache::put($cache_id, $text, 'categories');
}
$context['text'] .= $text;

// display extra information
$cache_id = 'categories/cloud.php#extra';
if(!$text =& Cache::get($cache_id)) {

	// add an extra box with helpful links
	$links = array('sections/' => i18n::s('Site map'),
		'search.php' => i18n::s('Search'),
		'help/' => i18n::s('Help index'),
		'query.php' => i18n::s('Contact'));
	$text .= Skin::build_box(i18n::s('See also'), Skin::build_list($links, 'compact'), 'extra')."\n";

	// save for later use
	Cache::put($cache_id, $text, 'articles');
}
$context['extra'] .= $text;

// referrals, if any
if(Surfer::is_associate() || (isset($context['with_referrals']) && ($context['with_referrals'] == 'Y'))) {

	$cache_id = 'categories/cloud.php#referrals#';
	if(!$text =& Cache::get($cache_id)) {

		// box content
		include_once '../agents/referrals.php';
		$text = Referrals::list_by_hits_for_url($context['url_to_root_parameter'].'categories/cloud.php');

		// in a sidebar box
		if($text)
			$text = Skin::build_box(i18n::s('Referrals'), $text, 'navigation', 'referrals');

		// save in cache for one hour 60 * 60 = 3600
		Cache::put($cache_id, $text, 'referrals', 3600);

	}

	// in the extra panel
	$context['extra'] .= $text;
}

// render the skin
render_skin();

?>