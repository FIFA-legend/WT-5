<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main</title>
    <link rel="stylesheet" href="static/main_styles.css" type="text/css">
</head>
<body>
<header>
    <div class="header-container">
        <span>E-mail</span>
    </div>
</header>
<main>
    <?php
    const regex = "/^(?:[a-zA-Z0-9]+(?:[-_.]?[a-zA-Z0-9]+)?@[a-zA-Z0-9_.-]+(?:\.?[a-zA-Z0-9]+)?\.[a-zA-Z]{2,5})$/i";
    $database_name = "localhost";
    $username = "root";
    $password = "0987654321KnKn";
    $table_name = "wt5";

    class Email
    {
        public int $id;
        public string $email;

        public function __construct(int $id, string $email)
        {
            $this->id = $id;
            $this->email = $email;
        }
    }

    $connection = mysqli_connect($database_name, $username, $password, $table_name);

    $is_duplicated = false;
    $is_wrong = false;
    $is_wrong_length = false;
    if (isset($_POST["email"])) {
        $input_email = trim($_POST["email"]);
        $count_request = "SELECT count(*) AS amount FROM emails WHERE email='$input_email';";
        $count_request_result = mysqli_query($connection, $count_request);
        $count = mysqli_fetch_assoc($count_request_result);
        if (strlen($input_email) > 60 || strlen($input_email) == 0) {
            $is_wrong_length = true;
        } else if ($count["amount"] > 0) {
            $is_duplicated = true;
        } else if (!preg_match(regex, $input_email)) {
            $is_wrong = true;
        } else {
            $save_request = "INSERT INTO emails (email) VALUES ('$input_email');";
            $res = mysqli_query($connection, $save_request);
        }
    }

    $get_request = "SELECT * FROM emails;";
    $result = mysqli_query($connection, $get_request);
    while ($row = mysqli_fetch_assoc($result)) {
        $email = new Email($row["id"], $row["email"]);
        $line = $email->id . ". " . $email->email;
        echo "<p>$line</p>";
    }

    mysqli_close($connection);
    ?>
    <form method="post" action="index.php">
        <input type="email" name="email" placeholder="Email:"> <br>
        <?php
        if ($is_duplicated) {
            echo "<p style='color: red;'>This email is already in database</p>";
        }
        if ($is_wrong) {
            echo "<p style='color: red;'>Incorrect email</p>";
        }
        if ($is_wrong_length) {
            echo "<p style='color: red;'>Incorrect  email length</p>";
        }
        ?>
        <button type="submit">Save</button>
    </form>
</main>
</body>
</html>