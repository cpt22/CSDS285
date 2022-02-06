<?php
require_once('tags.php');
$body = "";
if (isset($_GET) && isset($_GET['url_in'])) {
    $url = $_GET['url_in'];
    // Get contents of the URL
    $contents = file_get_contents($url);

    if (isset($_GET['tag_count'])) {
        // Extract all tags with regex
        $matches = array();
        $t = preg_match_all("/(?<=\<)[a-z]*/", $contents, $matches);
        $matches = array_filter($matches[0]);
        // Ensure no extraneous tags are there
        $matches = array_intersect($matches, $HTML_TAGS);

        // Count the number of each tag
        $tags = array();
        foreach ($matches as $match) {
            if (isset($tags[$match])) {
                $tags[$match] += 1;
            } else {
                $tags[$match] = 1;
            }
        }
        // Sort based on the number of each tag
        arsort($tags, SORT_NUMERIC);

        // Generate the table
        $body = <<<END
        <style>
            table, th, td {
                border: 1px solid black;
            }
        </style>
        <table>
            <tr>
                <th>Tag</th>
                <th>Count</th>
            </tr>
        END;
        foreach ($tags as $tag => $count) {
            $body .= "<tr><td>" . $tag . "</td><td>" . $count . "</td></tr>";
        }
        $body .= "</table>";
    } else if (isset($_GET['resize_image'])) {
        $matches = array();
        $t = preg_match_all("/(?<=\<img).*(?=\>)/", $contents, $matches);
        $matches = $matches[0];

        $links = array();

        foreach($matches as $match) {
            $vals = array();
            preg_match("/src=\"([^\"]+)\"/", $match, $vals);
            if (count($vals) > 0) {
                array_push($links, $vals[1]);
            }
        }

        foreach ($links as $link) {
            $body .= '<img src="' . $link . '" width="450" height="200"><br>';
        }
    }
} else {
    echo "There was an error";
}
?>

<html>
<head>
    <title>Results</title>
</head>
<body>
    <?php echo $body; ?>
</body>
</html>
