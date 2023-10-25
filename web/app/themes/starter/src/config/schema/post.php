<?php

// Disable post type "post"
if (apply_filters('themetik/config/schema/disable_post', true)) {
    add_filter('register_post_type_args', function ($args, $post_type) {
        if ('post' !== $post_type) {
            return $args;
        }
        $args['show_in_admin_bar'] = false;
        return $args;
    }, 10, 2);
    app()->schema()->unregister(app()->schema()->get('post', 'post'));
    app()->schema()->unregister(app()->schema()->get('comments', 'comment'));
}
