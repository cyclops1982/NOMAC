<?php
/**
 * @package NOMAC
 * @subpackage Theme
 * @since 2011
 */
?>
<?php
if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h2 class="entry-title">Niets gevonden...</h2>
		<div class="entry-content">
			Helaas konden we niets vinden met jou zoekopdracht. Probeer het nog eens!
			<?php get_search_form(); ?>
		</div>
	</div>
<?php endif; ?>




<?php

function PageNumbers() {
        global $wp_query, $paged;
        if (empty($paged) || $paged == 0) {
                $paged = 1;
        }
        $numPages = $wp_query->max_num_pages;
        if ($numPages > 1 && !is_single()) {
                $startItem = $paged - 4;
                if ($startItem <= 0) {
                        $startItem = 1;
                }
                $endItem = $paged + 4;
                if ($endItem > $numPages) {
                        $endItem = $numPages;
                }

                echo "<div class=\"paging\">";
                echo "<span>Pagina $paged van $numPages</span>";
                if ($paged > 1) {
                        if ($paged > 5) {
                                echo "<a href=\"".get_pagenum_link()."\" title=\"First\"><< First</a>";
                        }
                        previous_posts_link('< Vorige');
                }
                for ($i=$startItem;$i<=$endItem;$i++) {
                        if ($paged == $i) {
                                echo "<a class=\"current\" href=\"".get_pagenum_link($i)."\">$i</a>";
                        } else {
                                echo "<a href=\"".get_pagenum_link($i)."\">$i</a>";
                        }
                }
                if ($paged < $numPages) {
                        next_posts_link(' Volgende >');
                        if ($endItem < $numPages) {
                                echo "<a href=\"".get_pagenum_link($numPages)."\" title=\"Last\">Last >></a>";
                        }
                }
                echo "</div>";
        }
}



function permalink($title="") {
	echo '<a href="';
	the_permalink();
	echo '" title="';
	the_title_attribute();
	echo '" >';
	if (empty($title)) 
	{
		the_title();
	}
	else
	{
		echo $title;
	}
	echo '</a>';
}

	$postCount = 0;
	while(have_posts())
	{
		the_post();
		if (post_password_required())
		{
			continue;
		}
		echo '<div class="postsummary" id="post-'.get_the_ID().'" class="'.implode(' ',get_post_class()).'">';
		echo '<h2>';
		permalink();
		echo '</h2>';
		// show image
		echo '<div>';
		$img = get_post_thumb(get_the_ID());	
		if ($img != false)
		{
			if ($postCount % 2 == 0) 
			{
				echo '<img src="'.$img[0].'" align="left" class="leftimg" />';
			}
			else
			{
				echo '<img src="'.$img[0].'" align="right" class="rightimg" />';
			}
		}
		echo '<span class="postmeta">';
		the_time("Y/m/d");
		echo ' | Categorie&euml;n: ';
		the_category(', ');
		echo ' | Auteur: <a href="'.get_author_posts_url(get_the_author_meta('ID')).'" title="View all posts from '.get_the_author().'">'.get_the_author().'</a>';
		echo '</span>';
		echo '<p class="postcontent">';
		echo get_the_excerpt();
		echo '</p>';
		echo '<span class="readmore">';
		permalink('Lees verder...');
		echo '</span>';
		echo '</div>';
		echo '</div>';
		$postCount++;	
	}

	PageNumbers();

?>
