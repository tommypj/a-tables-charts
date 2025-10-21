<!DOCTYPE html>
<html>
<head>
    <title>Nonce Debug Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔐 Nonce Debug Test</h1>
    
    <div class="debug-box">
        <h2>Test Results:</h2>
        <div id="results"></div>
    </div>
    
    <div class="debug-box">
        <h2>Manual Test Form:</h2>
        <form id="test-form">
            <label>
                Select CSV File:
                <input type="file" id="test-file" accept=".csv" style="display:block; margin:10px 0;">
            </label>
            <button type="button" id="test-upload" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">
                Test Upload
            </button>
        </form>
        <div id="upload-result" style="margin-top:20px;"></div>
    </div>

    <script src="<?php echo includes_url('js/jquery/jquery.min.js'); ?>"></script>
    <script>
    jQuery(document).ready(function($) {
        const resultsDiv = $('#results');
        
        // Check if aTablesAdmin is defined
        if (typeof aTablesAdmin !== 'undefined') {
            resultsDiv.append('<p class="success">✓ aTablesAdmin object found</p>');
            resultsDiv.append('<pre>' + JSON.stringify(aTablesAdmin, null, 2) + '</pre>');
            
            // Verify nonce
            if (aTablesAdmin.nonce) {
                resultsDiv.append('<p class="success">✓ Nonce exists: ' + aTablesAdmin.nonce + '</p>');
                
                // Test nonce verification
                $.post(aTablesAdmin.ajaxUrl, {
                    action: 'atables_preview_import',
                    nonce: aTablesAdmin.nonce
                }, function(response) {
                    resultsDiv.append('<p class="info">Nonce test response:</p>');
                    resultsDiv.append('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                }).fail(function(xhr) {
                    resultsDiv.append('<p class="error">✗ Nonce test failed: ' + xhr.status + ' ' + xhr.statusText + '</p>');
                    resultsDiv.append('<pre>' + xhr.responseText + '</pre>');
                });
            } else {
                resultsDiv.append('<p class="error">✗ Nonce not found in aTablesAdmin</p>');
            }
        } else {
            resultsDiv.append('<p class="error">✗ aTablesAdmin object not found!</p>');
            resultsDiv.append('<p class="info">This means the JavaScript localization is not working.</p>');
        }
        
        // Manual upload test
        $('#test-upload').on('click', function() {
            const file = $('#test-file')[0].files[0];
            if (!file) {
                alert('Please select a file first');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'atables_preview_import');
            formData.append('nonce', aTablesAdmin.nonce);
            formData.append('file', file);
            formData.append('has_header', true);
            
            $('#upload-result').html('<p class="info">Uploading...</p>');
            
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#upload-result').html(
                        '<p class="success">✓ Upload successful!</p>' +
                        '<pre>' + JSON.stringify(response, null, 2) + '</pre>'
                    );
                },
                error: function(xhr) {
                    $('#upload-result').html(
                        '<p class="error">✗ Upload failed: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                        '<pre>' + xhr.responseText + '</pre>'
                    );
                }
            });
        });
    });
    </script>
</body>
</html>

<?php
// Load WordPress
require_once('../../../wp-load.php');

// Enqueue the admin script to get the nonce
wp_enqueue_script('a-tables-charts-admin', ATABLES_PLUGIN_URL . 'assets/js/admin-main.js', array('jquery'), ATABLES_VERSION, true);

wp_localize_script(
    'a-tables-charts-admin',
    'aTablesAdmin',
    array(
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('atables_admin_nonce'),
        'pluginUrl' => ATABLES_PLUGIN_URL,
    )
);

wp_print_scripts('a-tables-charts-admin');
?>