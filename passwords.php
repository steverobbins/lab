<?php

$characters = array(
    'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
    0, 1, 2, 3, 4, 5, 6, 7, 8, 9
);
$specials = array('!','#','$','*','&', '.');

include "readablewords.php";

?><html>
    <head>
        <title>Password Generator</title>
        <style type="text/css">

            hr {
                margin-top: 20px;
            }

            pre {
                white-space: pre-wrap;
                word-wrap: break-word;
            }

        </style>
    </head>
    <body>
        <?php

        $num = (int)(isset($_GET['num']) ? $_GET['num'] : 12);

        ?>
        <form method="get">
            <fieldset>
                <legend>Password Generator</legend>
                <label>Number of Characters: <input type="text" name="num" value="<?php echo $num ?>" /></label>
                <input type="submit" />
            </fieldset>
        </form>
        <pre><?php

        $total = count($characters) - 1;

        ?><h3>No Special Characters (<?php echo number_format(pow($total + 1, $num)) ?> combinations)</h3><?php

        for ($i = 0; $i < 150; $i++) {

            for ($j = 0; $j < $num; $j++) echo $characters[mt_rand(0, $total)];
            echo "\t";
        }

        $characters = array_merge($characters, $specials);
        $total = count($characters) - 1;

        ?><hr /><h3>Special Characters (<?php echo number_format(pow($total + 1, $num)) ?> combinations)</h3><?php

        for ($i = 0; $i < 150; $i++) {

            for ($j = 0; $j < $num; $j++) echo $characters[mt_rand(0, $total)];
            echo "\t";
        }

        $multiplier = 100;
        $readables = array();
        $limit = 200;
        $biggest = 0;

        foreach ($words as $group) {

            $total = count($group);
            $multiplier *= $total;
            $total -= 1;

            for ($i = 0; $i < $limit + 1; $i++)
                @$readables[$i] .= ucfirst($group[mt_rand(0, $total)]);
        }

        foreach ($readables as &$readable) {

            $readable .= mt_rand(0, 9) . mt_rand(0, 9);

            if (($len = strlen($readable)) > $biggest) $biggest = $len;
        }

        array_pop($readables);

        ?><hr /><h3>Readable (<?php echo number_format($multiplier) ?> combinations)</h3><?php

        foreach ($readables as $readable) echo str_pad($readable, $biggest + 1);

        ?></pre>
    </body>
</html>