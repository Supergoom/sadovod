<?php

class Comment_Walker extends Walker_Comment
{

    /**
     * Outputs a comment in the HTML5 format.
     *
     * @since Twenty Twenty 1.0
     *
     * @see wp_list_comments()
     * @see https://developer.wordpress.org/reference/functions/get_comment_author_url/
     * @see https://developer.wordpress.org/reference/functions/get_comment_author/
     * @see https://developer.wordpress.org/reference/functions/get_avatar/
     * @see https://developer.wordpress.org/reference/functions/get_comment_reply_link/
     * @see https://developer.wordpress.org/reference/functions/get_edit_comment_link/
     *
     * @param WP_Comment $comment Comment to display.
     * @param int        $depth   Depth of the current comment.
     * @param array      $args    An array of arguments.
     */
    protected function html5_comment($comment, $depth, $args)
    {
        $tag = ('div' === $args['style']) ? 'div' : 'li';

        $commenter          = wp_get_current_commenter();
        $show_pending_links = !empty($commenter['comment_author']);

        if ($commenter['comment_author_email']) {
            $moderation_note = __('Your comment is awaiting moderation.', 'oneunion');
        } else {
            $moderation_note = __('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'oneunion');
        }

        $rating = get_comment_rating($comment);

        $schema = 'itemprop="comment" itemscope itemtype="http://schema.org/Comment"';
        $text_schema = 'itemprop="text"';
        if ($rating > 0) {
            $schema = 'itemprop="review" itemscope itemtype="http://schema.org/Review"';
            $text_schema = 'itemprop="reviewBody"';
        }
?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body" <?= $schema ?>>
                <?php edit_comment_link('<i class="i-edit"></i>'); ?>
                <div class="comment-meta">
                    <div class="comment-author vcard">
                        <?php
                        if (0 != $args['avatar_size']) {
                            echo get_avatar($comment, $args['avatar_size']);
                        }
                        ?>

                        <div class="author-info">
                            <?php

                            $comment_author = get_comment_author_link($comment);
                            if ('0' == $comment->comment_approved && !$show_pending_links) {
                                $comment_author = get_comment_author($comment);
                            }

                            echo '<div itemscope itemprop="author" itemtype="http://schema.org/Person">
                                <span itemprop="name">' . $comment_author . '</span>
                            </div>'; ?>

                            <?php

                            echo '<meta itemprop="datePublished" content="' . get_comment_date('Y-m-d', $comment) . '">';
                            printf(
                                '<a class="comment-metadata" href="%s"><time datetime="%s">%s</time></a>',
                                esc_url(get_comment_link($comment, $args)),
                                get_comment_time('c'),
                                sprintf(
                                    /* translators: 1: Comment date, 2: Comment time. */
                                    __('%1$s at %2$s'),
                                    get_comment_date('', $comment),
                                    get_comment_time('')
                                )
                            );
                            ?>
                        </div>
                    </div><!-- .comment-author -->

                    <?php if ('0' == $comment->comment_approved) : ?>
                        <em class="comment-awaiting-moderation"><?php echo $moderation_note; ?></em>
                    <?php endif; ?>
                </div><!-- .comment-meta -->

                <div class="comment-content" <?= $text_schema ?>>
                    <?php comment_text(); ?>
                </div><!-- .comment-content -->

                <?php
                if ('1' == $comment->comment_approved || $show_pending_links) {
                    comment_reply_link(
                        array_merge(
                            $args,
                            array(
                                'add_below' => 'div-comment',
                                'depth'     => $depth,
                                'max_depth' => $args['max_depth'],
                                'before'    => '<div class="reply">',
                                'after'     => '</div>',
                            )
                        )
                    );
                }
                ?>
            </article><!-- .comment-body -->
    <?php
    }
}

remove_action('comment_post', 'wp_new_comment_notify_moderator');
remove_action('comment_post', 'wp_new_comment_notify_postauthor');

//TODO: Add custom comment notifications like after user autoriztion 

function notify_new_comment($comment_id)
{
    $notif_status = get_option('comments_notify');
    if ($notif_status) {

        $comment = get_comment($comment_id);
        if (empty($comment) || empty($comment->comment_post_ID)) {
            return false;
        }

        $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        if (is_multisite())
            $site_name = get_network()->site_name;

        $post   = get_post($comment->comment_post_ID);
        $author = get_userdata($post->post_author);
        $post_link = get_permalink($comment->comment_post_ID);

        /*  ----------------- Замена ------------------------------*/

        $replacments = array(
            '/{user_name}/'             => $author->display_name,
            '/{user_email}/ '           => $author->user_email,
            '/{user_login}/'            => $author->nickname,

            '/{site_name}/'             => $site_name,
            '/{unsub_url}/'             => home_url() . '?unsubcribe=' . $author->nickname . '&type=commentary',
            '/{login_url}/'             => home_url(),

            '/{post_url}/'              => $post_link,
            '/{post_title}/'            => $post->post_title,

            '/{post_author}/'           => $comment->comment_author,
            '/{post_author_email}/'     => $comment->comment_author_email,
            '/{post_author_url}/'       => $comment->comment_author_url,
            '/{post_author_ip}/'        => $comment->comment_author_IP,

            '/{post_author_comment}/'   => $comment->comment_content,
        );

        if (isset($comment->comment_approved) || '1' == $comment->comment_approved) {

            /*  ----------------- Пользователю ------------------------------*/

            $message = get_mail_styles();

            $message .= __(

                '<h1>Hello, {user_name}.</h1>
<p>You have received this letter because this email address assigned to account "{user_login}" on site "{site_name}".<br>
If you don’t wan’t recive this messages, follow the link: <a href="{unsub_url}">Unsubscribe from commentary notifications</a>.</p>

<p>You have received a new comment on the record `{post_title}`.</p>
<p>Author: {post_author}</p>
<p>Comment: {post_author_comment}</p>
<p><a href="{post_url}">Перейти</a></p>

<p>This letter is generated automatically and does not imply a response.<br>
Please, don’t answer it.</p>',
                'oneunion-mail'
            );

            $subject = sprintf(
                __('[%s] New Commentary', 'oneunion-mail'),
                $site_name
            );

            $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

            wp_mail($author->user_email, $subject, $message, array('content-type: text/html'));
        }

        /*  ----------------- Админу ------------------------------*/

        if ('0' == $comment->comment_approved) {
            $message = get_mail_styles();

            $message .= __(
                '<h1>Hello</h1>
<p>A new comment on the post `{post_title}` is waiting for approval on your site "{site_name}".</p>

<p>Author: {post_author}<br>
Email: {post_author_email}</p>
<p>Comment: {post_author_comment}</p>
<a href="{post_url}">Перейти</a>

<p>URL: {post_author_url}<br>
IP address: {post_author_ip}</p>    

<p>This letter is generated automatically and does not imply a response.<br>
Please, don’t answer it.</p>',
                'oneunion-mail'
            );

            $subject = sprintf(
                __('[%s] New commentary pending moderation', 'oneunion-mail'),
                $site_name
            );

            $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

            wp_mail(get_option('admin_email'), $subject, $message, array('content-type: text/html'));
        }
    }
}
add_action('comment_post', 'notify_new_comment');
