<?php
error_reporting(E_ALL);

/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package NOMAC 
 * @subpackage Theme
 * @since 2010
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html <?php language_attributes(); ?>>

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php

	//load jquery.
	wp_enqueue_script("jquery");


	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<script type="text/javascript">
	var _gaq = _gaq || []; 
	_gaq.push(['_setAccount', 'UA-26818352-1']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
</head>


<body <?php body_class(); ?>>
<div class="top maxwidth">
<a href="/"><img src="<?php bloginfo('template_url');?>/images/1x1.png" width="300" height="100" /></a>
</div>

<div class="maxwidth contentbg">	
	<div role="navigation" class="maxwidth navigation">

<?php
class MenuObject
{
        public $ID;
        public $Title;
        public $Url;
        public $Children;
        public $type;
        public $Parent;
        public $Selected;

        function __construct($menuitem)
        {
                $this->ID = $menuitem->ID;
                $this->Title = $menuitem->title;
                $this->Url = $menuitem->url;
                $this->Children = null; 
                $this->type = $menuitem->type;
                $this->Parent = null;
        }
}



function IsSelected($menuitem)
{
        global $wp_query;

        if (!isset($wp_query->queried_object_id) || $wp_query->queried_object_id == 0)
        {
                return false;
        }
        $queryObj = $wp_query->queried_object;

        if ($menuitem->type == "post_type" && $menuitem->object == "page") 
        {
                if (!isset($queryObj->taxonomy) && $queryObj->ID == $menuitem->object_id)
                {
                        return true;
                }
        }

        if ($menuitem->type == "taxonomy" && $menuitem->object == "category")
        {
                if (isset($queryObj->taxonomy) && $menuitem->object_id == $queryObj->term_id)
                {
                        return true;
                }
        }

        return false;
}

function GetChildren($menuitems, $parentid, $parent) {
	
	$childs = Array();
	foreach ($menuitems as $key => $menuitem) 
	{
		if ($menuitem->menu_item_parent == $parentid)
		{
			$item = new MenuObject($menuitem);
			$item->Parent = $parent;
			$item->Selected = IsSelected($menuitem);
			if ($item->Selected)
			{
				$tmp = $item;
				while($tmp->Parent != null)
				{
					$tmp = $tmp->Parent;
					$tmp->Selected = true;
				}
			}
			$item->Children = GetChildren($menuitems, $item->ID, $item);
			$childs[$menuitem->ID] = $item;		
		}
	}
	if (count($childs) == 0)
	{
		return null;
	}
	return $childs;
}

function GetChildrenById($menuObjects, $id) 
{
	foreach ($menuObjects as $key => $val) {
		if ($key == $id)
		{
			if (isset($val->Children))
			{
				return $val->Children;
			}
		}
		if (isset($val->Children))
		{
			$val = GetChildrenById($val->Children, $id);
			if ($val != null)
			{
				return $val;
			}
		}
	
	}
	return null;
}



$locations = get_nav_menu_locations();
$menulocations = wp_get_nav_menu_object( $locations['primary'] );
$menuitems = wp_get_nav_menu_items($menulocations->name);
$menuObjects = GetChildren($menuitems, 0, NULL);

$submenuId = 0;
function PrintMenuUl($items, $ulCssClass, $deep, &$submenuId, $maxdepth) {
	$deep++;
	if ($maxdepth < $deep)
	{
		return;
	}
	if (isset($items) && count($items) > 0)
	{
		echo "<ul class=\"".$ulCssClass."\" >\n";
		foreach ($items as $key => $val)
		{
			echo "<li ";
			if ($val->Selected)
			{
				echo " class=\"selected\" ";
				if ($deep > 1) 
				{
					$submenuId = $key;
				}
			}
			echo "><a href=\"".$val->Url."\" alt=\"".$val->Title."\" >".$val->Title."</a>\n";
			if (isset($val->Children))
			{
				PrintMenuUl($val->Children, "submenu submenu-$deep", $deep, $submenuId, $maxdepth);
			}
			echo "</li>";
		}

		echo "</ul>\n";
	}
}

PrintMenuUl($menuObjects, "headermenu", 0, $submenuId, 2);

?>
	<div id="searchbox">
		<?php
			get_search_form();
		?>
	</div>
	</div> <!-- / role=navigation -->

        <!-- Begin of MAIN CONTENT.  -->
         <div class="leftside">
	                <?
				if ($submenuId != 0) {
					$childs = GetChildrenById($menuObjects, $submenuId);
					if (isset($childs)) {
						echo "<div class=\"content subnavigation\" role=\"navigation\">";
						PrintMenuUl($childs, "pagemenu", 0, $a, 1);
						echo "</div>";
					}
				}
	                ?>

