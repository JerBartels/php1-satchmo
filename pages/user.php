<?php

//classes
include_once "init.php" ;

//specific pages
include_once "session.php";
include_once "reglog.php";


//Set default timezone
date_default_timezone_set(date_default_timezone_get());

//kijken of user al ingelogd is
if(!isset($_SESSION["username"]))
{
    header("location: ../index.php");
}

if(isset($_GET["username"]))
{
    try
    {
        $user = new User();
        $selected_user = $user->getUserByUsername($_GET["username"]);
        $active_user = $user->getUserByUsername($_SESSION["username"]);

        $post = new Post();
        $results = $post->getAllPosts();

        $follow = new Follow();
    }
    catch(Exception $e)
    {
        echo $e;
    }
}

if(isset($_POST["btn_love"]))
{
    try
    {
        $follow->Fan = $active_user["username"];
        $follow->Target = $selected_user["username"];

        if($selected_user["private"])
        {
            $follow->Accepted = false;
        }
        else
        {
            $follow->Accepted = true;
        }

        $follow->Save();
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
}

if(isset($_POST["btn_hate"]))
{
    try
    {
        $follow->Fan = $active_user["username"];
        $follow->Target = $selected_user["username"];
        $follow->DeleteFollow($follow->Fan, $follow->Target);
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Satchmo.cc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,700italic,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../styles/reset.css">
    <link rel="stylesheet" href="../styles/satchmo.css">
</head>

<body>

<nav>
    <div class="nav_content">

        <div class="nav_left">
            <a href="../index.php" class="nav_back a_nav">back</a>
        </div>

        <div class="clearfix"></div>

        <div class="nav_right">
            <a class="a_search a_nav" href="#">search</a>
            <a class="a_profile a_nav" href="profile.php">profile</a>
            <a class="a_logout a_nav" href="logout.php">logout</a>
        </div>

        <div class="clearfix"></div>

        <div class="nav_search">
            <form method="post" action="search.php" class ="form_nav" autocomplete="off">
                <input type="text" placeholder="search" class="submit_input" name="input_search">
                <input type="submit" value="find" name="submit_search" class="submit_search" id="submit_search">
            </form>
        </div>

        <div class="clearfix"></div>

    </div>
</nav>

<div>

    <div class="summary">
        <div class="summary_content">
            <div>
                <h1><?php echo $selected_user["username"] ?></h1>
                <h3><?php echo $selected_user["firstname"] . " " . $selected_user["lastname"] ?></h3>
                <div class="profile_pict">
                    <img src="../assets/<?php echo $selected_user["profilepic"] ?>" alt="profile-pic">
                </div>
            </div>

            <div>
                <?php
                    if($follow->AlreadyFan($active_user["username"],$selected_user["username"])){
                ?>
                        <form action="" method="post">
                            <input type="submit" value="break my heart" name="btn_hate" class="btn_follow">
                        </form>
                <?php
                    }
                    else {
                ?>
                        <form action="" method="post">
                            <input type="submit" value="love me" name="btn_love" class="btn_follow">
                        </form>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="results">
        <div class="results_content">
            <?php

            $follow = new Follow();

            //als user public is mogen de posts altijd getoond worden
            if(!$selected_user["private"])
            {
                foreach ($results as $result)
                {
                    if ($result["username"] == $selected_user["username"])
                    {
                        ?>

                        <figure class="figure_square <?php echo $result["filter"] ?>">
                            <a href="detail.php?post=<?php echo $result["id"] ?>">
                                <img class=results_results src="../assets/posts/<?php echo $result["photo"] ?>" alt="<?php echo $_POST["input_search"] ?>">
                            </a>
                        </figure>

                        <?php
                    }
                }
            }

            //als user private is mogen de posts enkel getoond worden als de follow geaccepteerd werd
            else
            {
                if($follow->AlreadyAcceptedFan($active_user["username"],$selected_user["username"]) || $_SESSION["username"] == $selected_user["username"])
                {
                    foreach($results as $result)
                    {
                        if($result["username"] == $selected_user["username"])
                        {
                            ?>
                            <div class="figure_cell">
                                <figure class="figure_square <?php echo $result["filter"] ?>">
                                    <a href="detail.php?post=<?php echo $result["id"]?>">
                                        <img class=results_results src="../assets/posts/<?php echo $result["photo"] ?>" alt="<?php echo $_POST["input_search"] ?>">
                                    </a>
                                </figure>
                            </div>


            <?php
                        }
                    }
                }
            }

            ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <div id="footer">

    </div>


</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="../scripts/script.js"></script>
</body>
</html>