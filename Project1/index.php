<?php
require_once('helpers.php');
$body = "URL must begin with http:// or https://";
if (isset($_GET) && isset($_GET['url']) && preg_match("/https?\:\/\//", $_GET['url'])) {
    $url = $_GET['url'];
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
        <table class="table">
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
        // Extract all image tags
        $t = preg_match_all("/(?<=\<img).*(?=\>)/", $contents, $matches);
        if (count(matches) > 0) {
            $body = <<<END
            Some images may not appear due to cross-origin request blocking on remote web servers.<br>
            END;

            $matches = $matches[0];
            $links = array();
            // Loop through all the matched tags and extract the url from the tag.
            foreach ($matches as $match) {
                $vals = array();
                preg_match("/src=\"([^\"]+)\"/", $match, $vals);
                if (count($vals) > 0) {
                    array_push($links, $vals[1]);
                }
            }
            // Add a resized image tag.
            foreach ($links as $link) {
                if (!preg_match("/https?\:\/\//", $link)) {
                    $link = relativeToAbsolute($link, $url);
                }
                $body .= '<img src="' . $link . '" class="img-fluid pb-3"><br>';
            }
        }
    }
} else {
    if (!isset($_GET['url']) && (isset($_GET['resize_image']) || isset($_GET['tag_count']))) {
        $body = "URL must be filled out";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project 1 - cpt15</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container py-5 shadow">
        <div class="pb-4 mx-3">
            <form method="get" action="" class="pb-4">
                <div class="input-group">
                    <!--<label for="url_in">Enter a URL:</label>-->
                    <input id="url_in" class="form-control" placeholder="Enter a URL" type="text" name="url" <?php echo "value=\"{$_GET['url']}\"" ?> required>
                    <button type="submit" name="tag_count" class="btn btn-outline-primary">Count Tags</button>
                    <button type="submit" name="resize_image" class="btn btn-outline-primary">Resize Images</button>
                </div>
            </form>
            <?php echo $body; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>