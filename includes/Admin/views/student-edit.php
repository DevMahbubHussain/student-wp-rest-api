<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Edit Student Info', 'student-info'); ?></h1>
    <?php if (isset($_GET['student-updated'])) { ?>
        <div class="notice notice-success">
            <p><?php _e('Student Information has been updated successfully!', 'student-manager'); ?></p>
        </div>
    <?php } ?>

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr class="row<?php echo $this->has_error('name') ? ' form-invalid' : ''; ?>">
                    <th scope="row">
                        <label for="name"><?php _e('Name', 'student-info'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_attr($student->name); ?>">
                        <?php if ($this->has_error('name')) { ?>
                            <p class="description error"><?php echo $this->get_error('name'); ?></p>
                        <?php } ?>
                    </td>
                </tr>
                <tr class="row<?php echo $this->has_error('address') ? ' form-invalid' : ''; ?>">
                    <th scope="row">
                        <label for="address"><?php _e('Address', 'student-info'); ?></label>
                    </th>
                    <td>
                        <textarea class="regular-text" name="address" id="address"><?php echo esc_textarea($student->address); ?></textarea>
                        <?php if ($this->has_error('address')) { ?>
                            <p class="description error"><?php echo $this->get_error('address'); ?></p>
                        <?php } ?>
                    </td>
                </tr>
                <tr class="row<?php echo $this->has_error('phone') ? ' form-invalid' : ''; ?>">
                    <th scope="row">
                        <label for="phone"><?php _e('Phone', 'student-info'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="phone" id="phone" class="regular-text" value="<?php echo esc_attr($student->phone); ?>">
                        <?php if ($this->has_error('phone')) { ?>
                            <p class="description error"><?php echo $this->get_error('phone'); ?></p>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="id" value="<?php echo esc_attr($student->id); ?>">
        <?php wp_nonce_field('new-student-info'); ?>
        <?php submit_button(__('Edit Student', 'student-info'), 'primary', 'submit_student_info'); ?>
    </form>
</div>