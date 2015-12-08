<?php
/**
 * Handles the plugin help screen.
 *
 * @author  Johan Steen <artstorm at gmail dot com>
 * @link    http://johansteen.se/
 */
class PostSnippets_Help
{
    /**
     * Constructor.
     *
     * @since   Post Snippets 1.8.9
     * @param   string  The option page to load the help text on
     */
    public function __construct($optionPage)
    {
        add_action('load-'.$optionPage, array(&$this,'addHelpTabs'));
    }

    /**
     * Setup the help tabs and sidebar.
     *
     * @since   Post Snippets 1.8.9
     */
    public function addHelpTabs()
    {
        $screen = get_current_screen();
        $screen->set_help_sidebar($this->helpSidebar());
        $screen->add_help_tab(
            array(
            'id'      => 'basic-plugin-help',
            'title'   => __('Basic', PostSnippets::TEXT_DOMAIN),
            'content' => $this->helpBasic()
            )
        );
        $screen->add_help_tab(
            array(
            'id'      => 'shortcode-plugin-help',
            'title'   => __('Shortcode', PostSnippets::TEXT_DOMAIN),
            'content' => $this->helpShortcode()
            )
        );
        if (!defined('POST_SNIPPETS_DISABLE_PHP')) {
            $screen->add_help_tab(
                array(
                'id'      => 'php-plugin-help',
                'title'   => __('PHP', PostSnippets::TEXT_DOMAIN),
                'content' => $this->helpPhp()
                )
            );
        }
        $screen->add_help_tab(
            array(
            'id'      => 'advanced-plugin-help',
            'title'   => __('Advanced', PostSnippets::TEXT_DOMAIN),
            'content' => $this->helpAdvanced()
            )
        );
    }

    /**
     * The right sidebar help text.
     * 
     * @return  string  The help text
     */
    public function helpSidebar()
    {
        return PostSnippets_View::render('help_sidebar');
    }

    /**
     * The basic help tab.
     * 
     * @return  string  The help text
     */
    public function helpBasic()
    {
        return PostSnippets_View::render('help_basic');
    }

    /**
     * The shortcode help tab.
     * 
     * @return  string  The help text
     */
    public function helpShortcode()
    {
        return PostSnippets_View::render('help_shortcode');
    }

    /**
     * The PHP help tab.
     * 
     * @return  string  The help text
     */
    public function helpPhp()
    {
        return PostSnippets_View::render('help_php');
    }

    /**
     * The advanced help tab.
     * 
     * @return  string  The help text
     */
    public function helpAdvanced()
    {
        return PostSnippets_View::render('help_advanced');
    }
}
