<p>
<?php _e('Snippets defined as shortcodes can optionally also be evaluated as PHP Code by enabling the PHP checkbox. PHP snippets is only available when treating the snippet as a shortcode.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p><strong><?php _e('Example PHP Snippet', PostSnippets::TEXT_DOMAIN); ?></strong><br/>
<code>
for ($i=1; $i<5; $i++) {<br/>
    echo "{loop_me}&lt;br/&gt;";<br/>
};
</code></p>

<p>
<?php _e('With a snippet defined like the one above, you can call it with its shortcode definition in a post. Let\'s pretend that the example snippet is named phpcode and have one variable defined loop_me, then it would be called like this from a post:', PostSnippets::TEXT_DOMAIN); ?>
</p>

<code>[phpcode loop_me="post snippet with PHP!"]</code>

<p>
<?php _e('When the shortcode is executed the loop_me variable will be replaced with the string supplied in the shortcode and then the PHP code will be evaluated. (Outputting the string five times in this case. Wow!)', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p>
<?php _e('Note the evaluation order, any snippet variables will be replaced before the snippet is evaluated as PHP code. Also note that a PHP snippet don\'t need to be wrapped in &lt;?php #code; ?&gt;.', PostSnippets::TEXT_DOMAIN); ?>
</p>
