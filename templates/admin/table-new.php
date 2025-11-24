<?php
/**
 * Admin New Table Template
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Add New Table', 'a-tables-charts' ); ?></h1>

    <div class="atables-upload-container" style="max-width: 800px;">
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Upload File', 'a-tables-charts' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Upload an Excel (.xlsx, .xls) or CSV file to create a new table.', 'a-tables-charts' ); ?>
            </p>

            <form id="atables-upload-form" enctype="multipart/form-data">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="table-title"><?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="table-title" name="title" class="regular-text" placeholder="<?php esc_attr_e( 'Enter table title', 'a-tables-charts' ); ?>">
                            <p class="description"><?php esc_html_e( 'Optional. If not provided, filename will be used.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="table-description"><?php esc_html_e( 'Description', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <textarea id="table-description" name="description" class="large-text" rows="3" placeholder="<?php esc_attr_e( 'Enter table description', 'a-tables-charts' ); ?>"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="table-file"><?php esc_html_e( 'File', 'a-tables-charts' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="file" id="table-file" name="file" accept=".xlsx,.xls,.csv" required>
                            <p class="description"><?php esc_html_e( 'Supported formats: Excel (.xlsx, .xls), CSV (.csv)', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large" id="upload-button">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php esc_html_e( 'Upload and Create Table', 'a-tables-charts' ); ?>
                    </button>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="button button-large">
                        <?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?>
                    </a>
                </p>

                <div id="upload-progress" style="display: none; margin-top: 20px;">
                    <div class="notice notice-info inline">
                        <p>
                            <span class="dashicons dashicons-update dashicons-spin"></span>
                            <strong><?php esc_html_e( 'Uploading and processing file...', 'a-tables-charts' ); ?></strong>
                        </p>
                    </div>
                </div>

                <div id="upload-result" style="display: none; margin-top: 20px;"></div>
            </form>
        </div>

        <!-- Manual Creation Option -->
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Or Create Manually', 'a-tables-charts' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Create an empty table and add data manually.', 'a-tables-charts' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create-manual' ) ); ?>" class="button button-secondary button-large">
                    <span class="dashicons dashicons-plus-alt" style="margin-top: 4px;"></span>
                    <?php esc_html_e( 'Create Empty Table', 'a-tables-charts' ); ?>
                </a>
            </p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#atables-upload-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const fileInput = $('#table-file')[0];
        const button = $('#upload-button');

        // Validate file
        if (!fileInput.files.length) {
            alert('<?php esc_html_e( 'Please select a file to upload.', 'a-tables-charts' ); ?>');
            return;
        }

        // Show progress
        $('#upload-progress').show();
        $('#upload-result').hide();
        button.prop('disabled', true);

        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'atables_upload_file');
        formData.append('nonce', atablesAdmin.nonce);
        formData.append('file', fileInput.files[0]);
        formData.append('title', $('#table-title').val());
        formData.append('description', $('#table-description').val());

        // Upload file
        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#upload-progress').hide();

                if (response.success) {
                    $('#upload-result').html(
                        '<div class="notice notice-success inline"><p><strong>' +
                        response.data.message +
                        '</strong></p></div>'
                    ).show();

                    // Redirect to table edit page after 2 seconds
                    setTimeout(function() {
                        window.location.href = '<?php echo admin_url( 'admin.php?page=a-tables-charts-edit&id=' ); ?>' + response.data.table_id;
                    }, 2000);
                } else {
                    $('#upload-result').html(
                        '<div class="notice notice-error inline"><p><strong>' +
                        response.data.message +
                        '</strong></p></div>'
                    ).show();
                    button.prop('disabled', false);
                }
            },
            error: function() {
                $('#upload-progress').hide();
                $('#upload-result').html(
                    '<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'An error occurred while uploading the file. Please try again.', 'a-tables-charts' ); ?></strong></p></div>'
                ).show();
                button.prop('disabled', false);
            }
        });
    });
});
</script>

<style>
.required {
    color: #d63638;
}
</style>
