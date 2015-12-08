<p>
<?php _e('You can retrieve a Post Snippet directly from PHP, in a theme for instance, by using the PostSnippets::getSnippet() method.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<h2><?php _e('Usage', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<code>
&lt;?php $my_snippet = PostSnippets::getSnippet( $snippet_name, $snippet_vars ); ?&gt;
</code></p>

<h2><?php _e('Parameters', PostSnippets::TEXT_DOMAIN); ?></h2>
<p>
<code>$snippet_name</code><br/>
<?php _e('(string) (required) The name of the snippet to retrieve.', PostSnippets::TEXT_DOMAIN); ?>
<br/><br/>
<code>$snippet_vars</code><br/>
<?php _e('(string) The variables to pass to the snippet, formatted as a query string.', PostSnippets::TEXT_DOMAIN); ?>
</p>

<h2><?php _e('Example', PostSnippets::TEXT_DOMAIN); ?></h2>
<p><code>
&lt;?php<br/>
    $my_snippet = PostSnippets::getSnippet( 'internal-link', 'title=Awesome&amp;url=2011/02/awesome/' );<br/>
    echo $my_snippet;<br/>
?&gt;
</code></p>
