    static public function a($href = '', $title = '', $target = "_self", $misc = '', $newline = true)
    {
        global $config;
        if(empty($title)) $title = $href;
        $newline = $newline ? "\n" : '';

        /* if page has onlybody param then add this param in all link. the param hide header and footer. */
        if(strpos($href, 'onlybody=yes') === false and isonlybody())
        {
            $onlybody = strpos($link, '?') === false ? "?onlybody=yes" : "&onlybody=yes";
            $href .= $onlybody;
        }

        if($target == '_self') return "<a href='$href' $misc>$title</a>$newline";
        return "<a href='$href' target='$target' $misc>$title</a>$newline";
    }
    static public function selectButton($scope = "", $appendClass = 'btn')
    {
        global $lang;
        return "<div class='checkbox $appendClass'><label><input type='checkbox' data-scope='$scope' class='rows-selector'> $lang->select</label></div>";
    }
