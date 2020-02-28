<?php
    $selector       = ( !empty($_GET['selector']) ) ? esc_attr__( $_GET['selector'] ) : '';
    $search_text    = ( !empty($_GET['search_text']) ) ? esc_attr__( $_GET['search_text'] ) : '';
?>

<div class="custom-search-container">
    <h2 class="heading-size-3">Property Search</h2>
    <form action="" method="get">
        <div class="form-group-search">
            <label for="custom-search-selector">Search by:</label>
            <select name="selector" class="search-selector" id="custom-search-selector" >
                <option value="address" <?php echo ($selector == 'address') ? ' selected ' : ''; ?>>Location</option>
                <option value="community_name" <?php echo ($selector == 'community_name') ? ' selected ' : ''; ?>>Name</option>
            </select>
        </div>
        <div class="form-group-search">
            <input type="text" name="search_text" class="search-field" placeholder="zip, address, or city" value="<?php echo $search_text; ?>">
        </div>
        <div class="form-group-search">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>