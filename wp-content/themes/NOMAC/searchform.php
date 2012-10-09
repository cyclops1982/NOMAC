<form action="<?php home_url('/'); ?>" method="get">
    <fieldset>
        <label for="search">Zoeken</label>
        <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" />
    </fieldset>
</form>
