<?php
if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>
<section class="comments-block">
  <div class="comments-block__head">
    <p class="eyebrow"><?php esc_html_e('Обговорення', 'vkusno-doma'); ?></p>
    <h2>
      <?php
      printf(
          esc_html(_n('%s коментар', '%s коментарів', get_comments_number(), 'vkusno-doma')),
          number_format_i18n(get_comments_number())
      );
      ?>
    </h2>
  </div>

  <?php if (have_comments()) : ?>
    <ol class="comment-list">
      <?php
      wp_list_comments([
          'style' => 'ol',
          'short_ping' => true,
          'avatar_size' => 56,
          'reply_text' => __('Відповісти', 'vkusno-doma'),
      ]);
      ?>
    </ol>
  <?php endif; ?>

  <?php
  comment_form([
      'title_reply' => __('Залишити коментар', 'vkusno-doma'),
      'title_reply_to' => __('Відповісти на коментар %s', 'vkusno-doma'),
      'title_reply_before' => '<h3 class="comments-form__title">',
      'title_reply_after' => '</h3>',
      'class_submit' => 'button button--primary',
      'label_submit' => __('Надіслати', 'vkusno-doma'),
      'comment_notes_before' => '',
      'comment_notes_after' => '',
      'logged_in_as' => is_user_logged_in() ? '<p class="logged-in-as">' . sprintf(
          wp_kses(
              __('Ви увійшли як %1$s. <a href="%2$s">Вийти?</a>', 'vkusno-doma'),
              ['a' => ['href' => []]]
          ),
          esc_html(wp_get_current_user()->display_name),
          esc_url(wp_logout_url(get_permalink()))
      ) . '</p>' : '',
      'cancel_reply_link' => __('Скасувати відповідь', 'vkusno-doma'),
      'fields' => [
          'author' => '<p class="comment-form-author"><label for="author">' . esc_html__('Ім’я', 'vkusno-doma') . ' <span class="required">*</span></label><input id="author" name="author" type="text" value="" size="30" maxlength="245" autocomplete="name" required /></p>',
          'email' => '<p class="comment-form-email"><label for="email">' . esc_html__('E-mail', 'vkusno-doma') . ' <span class="required">*</span></label><input id="email" name="email" type="email" value="" size="30" maxlength="100" autocomplete="email" required /></p>',
      ],
      'comment_field' => '<p class="comment-form-comment"><label for="comment">' . esc_html__('Коментар', 'vkusno-doma') . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required></textarea></p>',
      'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" /><label for="wp-comment-cookies-consent">' . esc_html__('Зберегти мої ім’я та e-mail для наступного коментаря.', 'vkusno-doma') . '</label></p>',
  ]);
  ?>
</section>
