<?php
/**
 * Display elements as a mosaic of blocks.
 * Use masonry.js
 * 
 * @author Alexis Raimbault
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */
class Layout_as_mosaic extends Layout_interface {
    
    function layout($result) {
	
	global $context;
	
	// we return some text
	$text = '';	
	
	// empty list
	if(!SQL::count($result))
	    return $text;
	
	// wrapper
	$text .= '<div class="mc-wrap" >'."\n";
	
	while($item = SQL::fetch($result)) {
	    
	    // get the object interface, this may load parent and overlay
	    $entity = new $this->listed_type($item);
	    
	    // link
	    $url = $entity->get_permalink();
	    
	    // title
	    $title = $entity->get_title();
	    
	    // intro
	    $intro = Codes::beautify_introduction($entity->get_introduction());
	    if($intro)
		$intro = BR.$intro;
	    
	    // image	  	    	   	    
	    if($thumb = trim($entity->get_thumbnail_url())) {	    	
		
		 // use parameter of the control panel for this one
		$options = '';
		if(isset($context['classes_for_thumbnail_images']))
			$options = 'class="'.$context['classes_for_thumbnail_images'].'" ';
		
		// build the complete HTML element
		$thumb = '<img src="'.$thumb.'" alt="" title="'.encode_field($title).'" '.$options.' />'."\n";

		
	    } else
		$thumb = MAP_IMG;
	    
	    // use the image as a link to the target page
	    $thumb = Skin::build_link($url, $thumb, 'basic', $title);
	    
	    // list articles, if any
	    $childs = $entity->get_childs('articles', 0, 5, 'alistapart');
	    
	    // content
	    $content = $thumb.$intro;
	    if(isset($childs['article']))
		$content .= $childs['article'];
	    
	    // add a block, guess the html tag from context
	    if(isset($context['SKIN_HTML5'])) {
		switch($this->listed_type) {

		    case'article':
			$tag = 'article';
			break;
		    case 'section':
		    case 'category':
			$tag = 'section';
			break;
		    case 'user':
		    default:
			$tag = 'div';
		}
	    } else
		$tag = 'div';
	    
	    $text .= '<'.$tag.' class="mc-block">'.'<h3>'.$title.'</h3>'.$content.'</'.$tag.'>'."\n";
	    
	}
	
	// end wrapper
	$text .= '</div>'."\n";
	
	// we have bound styles and scripts
	$this->load_scripts_n_styles();
	
	// initialize js
	Page::insert_script('Mosaic.init()');
	
	// end of processing
	SQL::free($result);
	return $text;
    }
    
}
?>
