<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Student Info', 'student-info'); ?></h1>
    <a class="page-title-action" href="<?php echo  admin_url('admin.php?page=student-manager&action=new') ?>">Add New Student</a>
    <?php if (isset($_GET['inserted'])) { ?>
        <div class="notice notice-success">
            <p><?php _e('Student Information has been inserted successfully!', 'student-manager'); ?></p>
        </div>
    <?php } ?>

    <?php if (isset($_GET['student-deleted'])) { ?>
        <div class="notice notice-success">
            <p><?php _e('Student Information has been deleted successfully!', 'student-manager'); ?></p>
        </div>
    <?php } ?>
    <form action="" method="post">
        <?php
        $table = new \Student\Manager\Admin\Student_List();
        $table->prepare_items();
        $table->search_box('search', 'search_id');
        $table->display();
        ?>
    </form>


</div>