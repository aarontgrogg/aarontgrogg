<h2><?php _e('Title', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<?php _e('Give the snippet a title that helps you identify it in the post editor. This also becomes the name of the shortcode if you enable that option', PostSnippets::TEXT_DOMAIN); ?>
</p>

<h2><?php _e('Variables', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<?php _e('A comma separated list of custom variables you can reference in your snippet. A variable can also be assigned a default value that will be used in the insert window by using the equal sign, variable=default.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p>
    <strong><?php _e('Example', PostSnippets::TEXT_DOMAIN); ?></strong><br/>
    <code>url,name,role=user,title</code>
</p>

<h2><?php _e('Snippet', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<?php _e('This is the block of text, HTML or PHP to insert in the post or as a shortcode. If you have entered predefined variables you can reference them from the snippet by enclosing them in {} brackets.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<p><strong><?php _e('Example', PostSnippets::TEXT_DOMAIN); ?></strong><br/>
<?php _e('To reference the variables in the example above, you would enter {url} and {name}. So if you enter this snippet:', PostSnippets::TEXT_DOMAIN); ?>
<br/>
<code>This is the website of &lt;a href="{url}"&gt;{name}&lt;/a&gt;</code>
<br/>
<?php _e('You will get the option to replace url and name on insert if they are defined as variables.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<h2><?php _e('Description', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<?php _e('An optional description for the Snippet. If filled out, the description will be displayed in the snippets insert window in the post editor.', PostSnippets::TEXT_DOMAIN); ?>
</p>
