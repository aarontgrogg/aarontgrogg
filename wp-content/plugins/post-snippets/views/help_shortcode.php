<p>
<?php _e('When enabling the shortcode checkbox, the snippet is no longer inserted directly but instead inserted as a shortcode. The obvious advantage of this is of course that you can insert a block of text or code in many places on the site, and update the content from one single place.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p>
<?php _e('The name to use the shortcode is the same as the title of the snippet (spaces are not allowed). When inserting a shortcode snippet, the shortcode and not the content will be inserted in the post.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p>
<?php _e('If you enclose the shortcode in your posts, you can access the enclosed content by using the variable {content} in your snippet. The {content} variable is reserved, so don\'t use it in the variables field.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<h2><?php _e('Options', PostSnippets::TEXT_DOMAIN); ?></h2>
<p><strong>PHP</strong><br/>
<?php _e('See the dedicated help section for information about PHP shortcodes.', PostSnippets::TEXT_DOMAIN); ?>
</p>
<p><strong>wptexturize</strong><br/>
<?php printf(__('Before the shortcode is outputted, it can optionally be formatted with %s, to transform quotes to smart quotes, apostrophes, dashes, ellipses, the trademark symbol, and the multiplication symbol.', PostSnippets::TEXT_DOMAIN), '<a href="http://codex.wordpress.org/Function_Reference/wptexturize">wptexturize</a>'); ?>
</p>
