<html>
    <head>
        <title>Australia Search Places</title>
    </head>
    <body>
    <form method="POST">
        <label>Suburb/Post Code: <input type="text" name="location" /></label>
        <br>
        <label>Distance: <input type="number" step="1" name="distance" /></label>
        <br>
        <button name="search" type="submit">Search</button>
    </form>

    <?php
        if (isset($_POST['search'])){

            require_once __DIR__.'/vendor/autoload.php';

            try {

                $pdo = new PDO('mysql:dbname=au_postalcodes;host=127.0.0.1', 'root', '1234');

                $places = (new ImmediateSolutions\Locator($pdo))->places($_POST['location'], $_POST['distance']);

                foreach ($places as $place){
                    echo '<p>'.$place['name'].' ('.$place['code'].')</p>';
                }
            } catch (Exception $exception){
                echo '<p style="color: red"><b>Error:</b> '.$exception->getMessage().'</p>';
            }
        }
    ?>

    </body>
</html>