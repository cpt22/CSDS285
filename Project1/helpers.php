<?php
$HTML_TAGS = ["a",
    "abbr",
    "acronym",
    "address",
    "applet",
    "area",
    "article",
    "aside",
    "audio",
    "b",
    "base",
    "basefont",
    "bdi",
    "bdo",
    "bgsound",
    "big",
    "blink",
    "blockquote",
    "body",
    "br",
    "button",
    "canvas",
    "caption",
    "center",
    "cite",
    "code",
    "col",
    "colgroup",
    "content",
    "data",
    "datalist",
    "dd",
    "decorator",
    "del",
    "details",
    "dfn",
    "dir",
    "div",
    "dl",
    "dt",
    "element",
    "em",
    "embed",
    "fieldset",
    "figcaption",
    "figure",
    "font",
    "footer",
    "form",
    "frame",
    "frameset",
    "h1",
    "h2",
    "h3",
    "h4",
    "h5",
    "h6",
    "head",
    "header",
    "hgroup",
    "hr",
    "html",
    "i",
    "iframe",
    "img",
    "input",
    "ins",
    "isindex",
    "kbd",
    "keygen",
    "label",
    "legend",
    "li",
    "link",
    "listing",
    "main",
    "map",
    "mark",
    "marquee",
    "menu",
    "menuitem",
    "meta",
    "meter",
    "nav",
    "nobr",
    "noframes",
    "noscript",
    "object",
    "ol",
    "optgroup",
    "option",
    "output",
    "p",
    "param",
    "plaintext",
    "pre",
    "progress",
    "q",
    "rp",
    "rt",
    "ruby",
    "s",
    "samp",
    "script",
    "section",
    "select",
    "shadow",
    "small",
    "source",
    "spacer",
    "span",
    "strike",
    "strong",
    "style",
    "sub",
    "summary",
    "sup",
    "table",
    "tbody",
    "td",
    "template",
    "textarea",
    "tfoot",
    "th",
    "thead",
    "time",
    "title",
    "tr",
    "track",
    "tt",
    "u",
    "ul",
    "var",
    "video",
    "wbr",
    "xmp"];


/* THIS METHOD WAS TAKEN FROM THE PHP DOCUMENTATION WEBSITE
 * IT CAN BE FOUND AT https://www.php.net/manual/en/function.parse-url.php#119033
 * POSTED IN A COMMENT BY mys5droid at gmail dot com
 */
function relativeToAbsolute($inurl, $absolute) {
    // Get all parts so not getting them multiple times :)
    $absolute_parts = parse_url($absolute);
    // Test if URL is already absolute (contains host, or begins with '/')
    if ( (strpos($inurl, $absolute_parts['host']) == false) ) {
        // Define $tmpurlprefix to prevent errors below
        $tmpurlprefix = "";
        // Formulate URL prefix    (SCHEME)
        if (!(empty($absolute_parts['scheme']))) {
            // Add scheme to tmpurlprefix
            $tmpurlprefix .= $absolute_parts['scheme'] . "://";
        }
        // Formulate URL prefix (USER, PASS)
        if ((!(empty($absolute_parts['user']))) and (!(empty($absolute_parts['pass'])))) {
            // Add user:port to tmpurlprefix
            $tmpurlprefix .= $absolute_parts['user'] . ":" . $absolute_parts['pass'] . "@";
        }
        // Formulate URL prefix    (HOST, PORT)
        if (!(empty($absolute_parts['host']))) {
            // Add host to tmpurlprefix
            $tmpurlprefix .= $absolute_parts['host'];
            // Check for a port, add if exists
            if (!(empty($absolute_parts['port']))) {
                // Add port to tmpurlprefix
                $tmpurlprefix .= ":" . $absolute_parts['port'];
            }
        }
        // Formulate URL prefix    (PATH) and only add it if the path to image does not include ./
        if ( (!(empty($absolute_parts['path']))) and (substr($inurl, 0, 1) != '/') ) {
            // Get path parts
            $path_parts = pathinfo($absolute_parts['path']);
            // Add path to tmpurlprefix
            $tmpurlprefix .= $path_parts['dirname'];
            $tmpurlprefix .= "/";
        }
        else {
            $tmpurlprefix .= "/";
        }
        // Lets remove the '/'
        if (substr($inurl, 0, 1) == '/') { $inurl = substr($inurl, 1); }
        // Lets remove the './'
        if (substr($inurl, 0, 2) == './') { $inurl = substr($inurl, 2); }
        return $tmpurlprefix . $inurl;
    }
    else {
        // Path is already absolute. Return it :)
        return $inurl;
    }
}

?>
