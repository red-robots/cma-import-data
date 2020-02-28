<h2>CMA Import Properties Data</h2>

<p>Import CSV file to populate the CMA custom post type <u>properties</u></p>
 <p>
    Note: Need to create <u>custom fields</u> before importing the CSV data with the following samples:
    <table  class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th class="manage-column column-columnname" scope="col">coupon_code</th>
                <th class="manage-column column-columnname" scope="col">community_name</th>
                <th class="manage-column column-columnname" scope="col">address</th>
                <th class="manage-column column-columnname" scope="col">manager_name</th>
                <th class="manage-column column-columnname" scope="col">manager_email</th>
                <th class="manage-column column-columnname" scope="col">manager_phone</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2923</td>
                <td>Sample Community name</td>
                <td>23 Johnson St.</td>
                <td>John Doe</td>
                <td>johndoe@demo.com</td>
                <td>998322332</td>
            </tr>
        </tbody>
    </table>
    
</p>

<form action="" method="post" enctype="multipart/form-data"> 
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