<div id="comments">

    <?php if (!post_password_required()) : ?>

        <?php if (have_comments()) : ?>

            <?php $total_comment_count = get_comments_number(); ?>
            <h4 id="comments-title" class="single-block-title">
                <?php printf(
                    _n('<span>%1$s</span> response to %2$s', '<span>%1$s</span> responses to %2$s', $total_comment_count, 'oneunion'),
                    $total_comment_count,
                    '<em>' . get_the_title() . '</em>'
                );
                ?>
            </h4>

            <?php if ($total_comment_count) : ?>
                <?php $review_count = get_comments('post_id=' . $post->ID . '&author__not_in=' . $post->post_author . '&meta_key=rating&meta_compare=EXISTS&count=true&status=approve'); ?>
                <?php $comment_count = $total_comment_count - $review_count; ?>

                <?php if ($review_count) : ?>
                    <meta itemprop="reviewCount" content="<?= $review_count ?>">
                <? endif; ?>

                <?php if ($comment_count) : ?>
                    <meta itemprop="commentCount" content="<?= $comment_count ?>">
                <? endif; ?>
            <? endif; ?>


            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <div class="navigation">
                    <div class="nav-previous"><?php previous_comments_link(__('<span class="meta-nav">&larr;</span> Older Comments', 'oneunion')); ?></div>
                    <div class="nav-next"><?php next_comments_link(__('Newer Comments <span class="meta-nav">&rarr;</span>', 'oneunion')); ?></div>
                </div>
            <?php endif; ?>

            <ol class="commentlist">
                <?php
                wp_list_comments(
                    array('avatar_size' => 50, 'walker' => new Comment_Walker())
                );
                ?>
            </ol>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? 
            ?>
                <div class="navigation">
                    <div class="nav-previous"><?php previous_comments_link(__('<span class="meta-nav">&larr;</span> Older Comments', 'oneunion')); ?></div>
                    <div class="nav-next"><?php next_comments_link(__('Newer Comments <span class="meta-nav">&rarr;</span>', 'oneunion')); ?></div>
                </div><!-- .navigation -->
            <?php endif; ?>

            <?php
            $num_comments = get_comments_number();
            if (!comments_open() && $num_comments == 0) : ?>
                <p class="nocomments"><?php _e('Comments are closed.', 'oneunion'); ?></p>
            <?php endif;  ?>

        <?php endif; ?>

        <?php if (comments_open()) :

            $comment_settings = array(
                'fields'               => [],
                'comment_field'        => '<div class="form-group">
						<textarea id="comment" class="form-control has-emoji" name="comment" placeholder="' . __('Enter Comment', 'oneunion') . '" rows="8" aria-required="true" required="required"></textarea>
					</div>',
                'must_log_in'          => '<p class="must-log-in">' .
                    sprintf(
                        __('You must be <a %s>logged in</a> to post a comment.', 'oneunion'),
                        'class="btn btn-link" data-bs-toggle="modal" data-bs-target="#loginForm"'
                    ) . '
					</p>',
                'logged_in_as'         => '',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
                'action'               => '',
                'id_form'              => 'commentform',
                'id_submit'            => 'submit',
                'class_container'      => 'comment-respond',
                'class_form'           => 'comment-form',
                'class_submit'         => 'btn btn-primary',
                'name_submit'          => 'submit',
                'title_reply'          => __('Leave a Reply', 'oneunion'),
                'title_reply_to'       => __('Leave a Reply to %s', 'oneunion'),
                'title_reply_before'   => '<h4 id="reply-title" class="single-block-title comment-reply-title">',
                'title_reply_after'    => '</h4>',
                'cancel_reply_before'  => ' <small>',
                'cancel_reply_after'   => '</small>',
                'cancel_reply_link'    => __('Cancel reply', 'oneunion'),
                'label_submit'         => __('Post Comment', 'oneunion'),
                'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s">',
                'submit_field'         => '%1$s %2$s',
                'format'               => 'xhtml',
            );

            comment_form($comment_settings);
        endif; ?>

    <?php else : ?>
        <p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'oneunion'); ?></p>
    <?php endif; ?>

</div><!-- #comments -->