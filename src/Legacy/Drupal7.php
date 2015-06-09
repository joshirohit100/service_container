<?php

/**
 * @file
 * Contains \Drupal\service_container\Legacy\Drupal7.
 */

namespace Drupal\service_container\Legacy;

/**
 * Defines the Drupal 7 legacy service.
 *
 * @method void flood_register_event(string $name, integer $window = 3600, string $identifier = NULL)
 * @method void flood_clear_event(string $name, string $identifier = NULL)
 * @method bool flood_is_allowed(string $name, integer $threshold, integer $window = 3600, string $identifier = NULL)
 * @method bool module_load_all(bool $bootstrap = FALSE)
 * @method array module_list(bool $refresh = FALSE, bool $bootstrap_refresh = FALSE, bool $sort = FALSE, array $fixed_list = NULL)
 * @method bool module_exists(string $module)
 * @method string drupal_get_filename(string $type, string $name, string $filename = NULL)
 * @method string module_load_include(string $type, string $module, string $name = NULL)
 * @method void module_load_all_includes(string $type, string $name = NULL)
 * @method array module_implements(string $hook, bool $sort = FALSE, bool $reset = FALSE)
 * @method void module_implements_write_cache()
 * @method void drupal_static_reset(string $name = NULL)
 * @method array system_rebuild_module_data()
 * @method array module_hook_info()
 * @method bool module_enable(array $module_list, bool $enable_dependencies = TRUE)
 * @method bool module_disable(array $module_list, bool $enable_dependencies = TRUE)
 * @method bool drupal_uninstall_modules(array $module_list = array(), bool $uninstall_dependents = TRUE)
 * @method void watchdog($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL)
 * @method bool user_access($string, $account = NULL)
 * @method bool user_is_logged_in()
 * @method bool user_is_anonymous()
 * @method string drupal_get_user_timezone()
 * @method string format_plural($count, $singular, $plural, array $args = array(), array $options = array())
 * @method mixed variable_get($name, $default = NULL)
 * @method void variable_set($name, $value)
 *
 */
class Drupal7 {

  public function __call($method, $args) {
    return call_user_func_array($method, $args);
  }

  /**
   * Formats an internal or external URL link as an HTML anchor tag.
   *
   * This function correctly handles aliased paths and adds an 'active' class
   * attribute to links that point to the current page (for theming), so all
   * internal links output by modules should be generated by this function if
   * possible.
   *
   * However, for links enclosed in translatable text you should use t() and
   * embed the HTML anchor tag directly in the translated string. For example:
   * @code
   * t('Visit the <a href="@url">settings</a> page', array('@url' => url('admin')));
   * @endcode
   * This keeps the context of the link title ('settings' in the example) for
   * translators.
   *
   * @param string $text
   *   The translated link text for the anchor tag.
   * @param string $path
   *   The internal path or external URL being linked to, such as "node/34" or
   *   "http://example.com/foo". After the url() function is called to construct
   *   the URL from $path and $options, the resulting URL is passed through
   *   check_plain() before it is inserted into the HTML anchor tag, to ensure
   *   well-formed HTML. See url() for more information and notes.
   * @param array $options
   *   An associative array of additional options. Defaults to an empty array. It
   *   may contain the following elements.
   *   - 'attributes': An associative array of HTML attributes to apply to the
   *     anchor tag. If element 'class' is included, it must be an array; 'title'
   *     must be a string; other elements are more flexible, as they just need
   *     to work in a call to drupal_attributes($options['attributes']).
   *   - 'html' (default FALSE): Whether $text is HTML or just plain-text. For
   *     example, to make an image tag into a link, this must be set to TRUE, or
   *     you will see the escaped HTML image tag. $text is not sanitized if
   *     'html' is TRUE. The calling function must ensure that $text is already
   *     safe.
   *   - 'language': An optional language object. If the path being linked to is
   *     internal to the site, $options['language'] is used to determine whether
   *     the link is "active", or pointing to the current page (the language as
   *     well as the path must match). This element is also used by url().
   *   - Additional $options elements used by the url() function.
   *
   * @return string
   *   An HTML string containing a link to the given path.
   *
   * @see url()
   *
   * @codeCoverageIgnore
   */
  public function l($text, $path, array $options = array()) {
    return l($text, $path, $options);
  }

  /**
   * Translates a string to the current language or to a given language.
   *
   * The t() function serves two purposes. First, at run-time it translates
   * user-visible text into the appropriate language. Second, various mechanisms
   * that figure out what text needs to be translated work off t() -- the text
   * inside t() calls is added to the database of strings to be translated.
   * These strings are expected to be in English, so the first argument should
   * always be in English. To enable a fully-translatable site, it is important
   * that all human-readable text that will be displayed on the site or sent to
   * a user is passed through the t() function, or a related function. See the
   * @link http://drupal.org/node/322729 Localization API @endlink pages for
   * more information, including recommendations on how to break up or not
   * break up strings for translation.
   *
   * @section sec_translating_vars Translating Variables
   * You should never use t() to translate variables, such as calling
   * @code t($text); @endcode, unless the text that the variable holds has been
   * passed through t() elsewhere (e.g., $text is one of several translated
   * literal strings in an array). It is especially important never to call
   * @code t($user_text); @endcode, where $user_text is some text that a user
   * entered - doing that can lead to cross-site scripting and other security
   * problems. However, you can use variable substitution in your string, to put
   * variable text such as user names or link URLs into translated text. Variable
   * substitution looks like this:
   * @code
   * $text = t("@name's blog", array('@name' => format_username($account)));
   * @endcode
   * Basically, you can put variables like @name into your string, and t() will
   * substitute their sanitized values at translation time. (See the
   * Localization API pages referenced above and the documentation of
   * format_string() for details about how to define variables in your string.)
   * Translators can then rearrange the string as necessary for the language
   * (e.g., in Spanish, it might be "blog de @name").
   *
   * @section sec_alt_funcs_install Use During Installation Phase
   * During the Drupal installation phase, some resources used by t() wil not be
   * available to code that needs localization. See st() and get_t() for
   * alternatives.
   *
   * @param $string
   *   A string containing the English string to translate.
   * @param $args
   *   An associative array of replacements to make after translation. Based
   *   on the first character of the key, the value is escaped and/or themed.
   *   See format_string() for details.
   * @param $options
   *   An associative array of additional options, with the following elements:
   *   - 'langcode' (defaults to the current language): The language code to
   *     translate to a language other than what is used to display the page.
   *   - 'context' (defaults to the empty context): The context the source string
   *     belongs to.
   *
   * @return
   *   The translated string.
   *
   * @see st()
   * @see get_t()
   * @see format_string()
   * @ingroup sanitization
   *
   * @codeCoverageIgnore
   */
  public function t($string, array $args = array(), array $options = array()) {
    return t($string, $args, $options);
  }

  /**
   * Generates an internal or external URL.
   *
   * When creating links in modules, consider whether l() could be a better
   * alternative than url().
   *
   * @param $path
   *   (optional) The internal path or external URL being linked to, such as
   *   "node/34" or "http://example.com/foo". The default value is equivalent to
   *   passing in '<front>'. A few notes:
   *   - If you provide a full URL, it will be considered an external URL.
   *   - If you provide only the path (e.g. "node/34"), it will be
   *     considered an internal link. In this case, it should be a system URL,
   *     and it will be replaced with the alias, if one exists. Additional query
   *     arguments for internal paths must be supplied in $options['query'], not
   *     included in $path.
   *   - If you provide an internal path and $options['alias'] is set to TRUE, the
   *     path is assumed already to be the correct path alias, and the alias is
   *     not looked up.
   *   - The special string '<front>' generates a link to the site's base URL.
   *   - If your external URL contains a query (e.g. http://example.com/foo?a=b),
   *     then you can either URL encode the query keys and values yourself and
   *     include them in $path, or use $options['query'] to let this function
   *     URL encode them.
   * @param $options
   *   (optional) An associative array of additional options, with the following
   *   elements:
   *   - 'query': An array of query key/value-pairs (without any URL-encoding) to
   *     append to the URL.
   *   - 'fragment': A fragment identifier (named anchor) to append to the URL.
   *     Do not include the leading '#' character.
   *   - 'absolute': Defaults to FALSE. Whether to force the output to be an
   *     absolute link (beginning with http:). Useful for links that will be
   *     displayed outside the site, such as in an RSS feed.
   *   - 'alias': Defaults to FALSE. Whether the given path is a URL alias
   *     already.
   *   - 'external': Whether the given path is an external URL.
   *   - 'language': An optional language object. If the path being linked to is
   *     internal to the site, $options['language'] is used to look up the alias
   *     for the URL. If $options['language'] is omitted, the global $language_url
   *     will be used.
   *   - 'https': Whether this URL should point to a secure location. If not
   *     defined, the current scheme is used, so the user stays on HTTP or HTTPS
   *     respectively. TRUE enforces HTTPS and FALSE enforces HTTP, but HTTPS can
   *     only be enforced when the variable 'https' is set to TRUE.
   *   - 'base_url': Only used internally, to modify the base URL when a language
   *     dependent URL requires so.
   *   - 'prefix': Only used internally, to modify the path when a language
   *     dependent URL requires so.
   *   - 'script': The script filename in Drupal's root directory to use when
   *     clean URLs are disabled, such as 'index.php'. Defaults to an empty
   *     string, as most modern web servers automatically find 'index.php'. If
   *     clean URLs are disabled, the value of $path is appended as query
   *     parameter 'q' to $options['script'] in the returned URL. When deploying
   *     Drupal on a web server that cannot be configured to automatically find
   *     index.php, then hook_url_outbound_alter() can be implemented to force
   *     this value to 'index.php'.
   *   - 'entity_type': The entity type of the object that called url(). Only
   *     set if url() is invoked by entity_uri().
   *   - 'entity': The entity object (such as a node) for which the URL is being
   *     generated. Only set if url() is invoked by entity_uri().
   *
   * @return
   *   A string containing a URL to the given path.
   */
  public function url($path = NULL, array $options = array()) {
    return url($path, $options);
  }

  /**
   * Invokes a hook in a particular module.
   *
   * All arguments are passed by value. Use drupal_alter() if you need to pass
   * arguments by reference.
   *
   * @param $module
   *   The name of the module (without the .module extension).
   * @param $hook
   *   The name of the hook to invoke.
   * @param ...
   *   Arguments to pass to the hook implementation.
   *
   * @return
   *   The return value of the hook implementation.
   *
   * @see drupal_alter()
   *
   * @codeCoverageIgnore
   */
  public function module_invoke($module, $hook) {
    $args = func_get_args();
    // Remove $module and $hook from the arguments.
    unset($args[0], $args[1]);

    return module_invoke($module, $hook, $args);
  }

  /**
   * Invokes a hook in all enabled modules that implement it.
   *
   * All arguments are passed by value. Use drupal_alter() if you need to pass
   * arguments by reference.
   *
   * @param $hook
   *   The name of the hook to invoke.
   * @param ...
   *   Arguments to pass to the hook.
   *
   * @return
   *   An array of return values of the hook implementations. If modules return
   *   arrays from their implementations, those are merged into one array.
   *
   * @see drupal_alter()
   *
   * @codeCoverageIgnore
   */
  public function module_invoke_all($hook) {
    $args = func_get_args();
    // Remove $hook from the arguments.
    unset($args[0]);

    return module_invoke_all($hook, $args);
  }

  /**
   * Passes alterable variables to specific hook_TYPE_alter() implementations.
   *
   * This dispatch function hands off the passed-in variables to type-specific
   * hook_TYPE_alter() implementations in modules. It ensures a consistent
   * interface for all altering operations.
   *
   * A maximum of 2 alterable arguments is supported (a third is supported for
   * legacy reasons, but should not be used in new code). In case more arguments
   * need to be passed and alterable, modules provide additional variables
   * assigned by reference in the last $context argument:
   * @code
   *   $context = array(
   *     'alterable' => &$alterable,
   *     'unalterable' => $unalterable,
   *     'foo' => 'bar',
   *   );
   *   drupal_alter('mymodule_data', $alterable1, $alterable2, $context);
   * @endcode
   *
   * Note that objects are always passed by reference in PHP5. If it is absolutely
   * required that no implementation alters a passed object in $context, then an
   * object needs to be cloned:
   * @code
   *   $context = array(
   *     'unalterable_object' => clone $object,
   *   );
   *   drupal_alter('mymodule_data', $data, $context);
   * @endcode
   *
   * @param $type
   *   A string describing the type of the alterable $data. 'form', 'links',
   *   'node_content', and so on are several examples. Alternatively can be an
   *   array, in which case hook_TYPE_alter() is invoked for each value in the
   *   array, ordered first by module, and then for each module, in the order of
   *   values in $type. For example, when Form API is using drupal_alter() to
   *   execute both hook_form_alter() and hook_form_FORM_ID_alter()
   *   implementations, it passes array('form', 'form_' . $form_id) for $type.
   * @param $data
   *   The variable that will be passed to hook_TYPE_alter() implementations to be
   *   altered. The type of this variable depends on the value of the $type
   *   argument. For example, when altering a 'form', $data will be a structured
   *   array. When altering a 'profile', $data will be an object.
   * @param $context1
   *   (optional) An additional variable that is passed by reference.
   * @param $context2
   *   (optional) An additional variable that is passed by reference. If more
   *   context needs to be provided to implementations, then this should be an
   *   associative array as described above.
   * @param $context3
   *   (optional) An additional variable that is passed by reference. This
   *   parameter is deprecated and will not exist in Drupal 8; consequently, it
   *   should not be used for new Drupal 7 code either. It is here only for
   *   backwards compatibility with older code that passed additional arguments
   *   to drupal_alter().
   *
   * @codeCoverageIgnore
   */
  public function drupal_alter($type, &$data, &$context1 = NULL, &$context2 = NULL, &$context3 = NULL) {
    drupal_alter($type, $data, $context1, $context2, $context3);
  }

  /**
   * Includes a file with the provided type and name.
   *
   * This prevents including a theme, engine, module, etc., more than once.
   *
   * @param $type
   *   The type of item to load (i.e. theme, theme_engine, module).
   * @param $name
   *   The name of the item to load.
   *
   * @return
   *   TRUE if the item is loaded or has already been loaded.
   *
   * @codeCoverageIgnore
   */
  public function drupal_load($type, $name) {
    return drupal_load($type, $name);
  }

}
