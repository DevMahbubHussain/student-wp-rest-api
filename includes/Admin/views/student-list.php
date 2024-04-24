<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Student Info', 'student-info'); ?></h1>
    <a class="page-title-action" href="<?php echo  admin_url('admin.php?page=student-manager&action=new') ?>">Add New Student</a>

    <form action="" method="post">
        <?php
        $table = new \Student\Manager\Admin\Student_List();
        $table->prepare_items();
        $table->search_box('search', 'search_id');
        $table->display();
        ?>
    </form>


</div>