<div class="student-contact-form" id="student-contact-form">

    <form action="" method="post">

        <div class="form-row">
            <label for="name"><?php _e('Name', 'student-manager'); ?></label>

            <input type="text" id="name" name="name" value="" required>
        </div>

        <div class="form-row">
            <label for="email"><?php _e('E-Mail', 'student-manager'); ?></label>

            <input type="email" id="email" name="email" value="" required>
        </div>

        <div class="form-row">
            <label for="message"><?php _e('Message', 'student-manager'); ?></label>

            <textarea name="message" id="message" required></textarea>
        </div>

        <div class="form-row">

            <?php wp_nonce_field('mh-contact-form'); ?>

            <input type="hidden" name="action" value="mh_contact_form">
            <input type="submit" name="send_enquiry" value="<?php esc_attr_e('Send Enquiry', 'student-manager'); ?>">
        </div>

    </form>
</div>