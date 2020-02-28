<form action="" method="post" enctype="multipart/form-data" style="margin:0 0 10px"> 
    <input type="hidden" name="_wp_http_referer" value="<?php echo get_admin_url(); ?>admin.php?page=cma-search-data&message=1">
    <div class="form-input-group">
        <input type="hidden" name="admin_url" id="admin_url" value="<?php echo admin_url('admin-ajax.php'); ?>">
    </div>
    <div class="form-input-group">
        <label for="average_sale" class="text-shadow text-white">Upload File (CSV):</label>
        <input type="file" class="form-input form-editable" name="cma_data_properties" id="cma_data_properties" >
    </div>
    <div class="form-input-group">
        <button type="submit" class="button button-primary" name="cma_search_submit_values">Import Data</button>
        <div class="clear-both"></div>
    </div>    
</form>