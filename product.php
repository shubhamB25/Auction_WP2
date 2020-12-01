<!DOCTYPE html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('templates/header.php');
include("templates/db.php");
?>


<style media="screen">
    .custom-file-input::-webkit-file-upload-button {
        visibility: hidden;
    }

    .custom-file-input::before {
        content: 'Upload pic';
        display: inline-block;
        background: linear-gradient(top, #f9f9f9, #e3e3e3);
        border: 1px solid #999;
        border-radius: 3px;
        padding: 5px 8px;
        outline: none;
        white-space: nowrap;
        -webkit-user-select: none;
        cursor: pointer;
        text-shadow: 1px 1px #fff;
        font-weight: 700;
        font-size: 10pt;

    }


    .custom-file-input:hover::before {
        border-color: black;
    }

    .custom-file-input:active::before {
        background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
    }

    #item-image {
        position: relative;
        padding: 10px;
        width: 500px;
    }

    #item-image img {
        width: 100px;
        height: 100px;
        border: 3px solid black;
        left: 100px;
    }
</style>
<?php
$item_id = $_GET['link'];
$sql = "SELECT * from auction_detail where id = ?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: login.php?error=sqlerror");
    exit();
} else {
    mysqli_stmt_bind_param($stmt, "s", $item_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    $_SESSION['bid_id'] = $item_id;
}

?>

<section class="row container grey-text center">
    <div class="col s6 ">

        <h4 class="center">Item Detail</h4>

        <form class="white" action="add_bid.php" method="POST" enctype="multipart/form-data">
            <div class="white" id="item-image">
                <img src="images/gym.jpg"><br>
                <p><?php echo htmlspecialchars($row['title']);  ?></p>
                <p>Category : <?php echo htmlspecialchars($row['category']);  ?></p>
                <p>Description : <?php echo htmlspecialchars($row['description']);  ?></p>
                <p>Minimum value: Rs <?php echo htmlspecialchars($row['base_amount']);  ?></p>
                <p>End Date <?php echo htmlspecialchars($row['due']);  ?></p>
                <br><br>
                <label>Enter value</label>
                <input type="number" name="amount" placeholder="100">
                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </div>
        </form>
    </div>

    <div class="col s6 ">
        <h3>Bids For this Item</h3>

        <?php
        $bid_sql = "SELECT * FROM bids WHERE p_id = ? ORDER BY amount DESC LIMIT 5";
        if (!mysqli_stmt_prepare($stmt, $bid_sql)) {
            header("Location: login.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $item_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $idx = 1 ; 
            while ($row2 = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        ?>

                <div class="whilte">
                    <p><?php echo $idx.") ". htmlspecialchars($row2['amount']);  ?></p>
                </div>

        <?php
        $idx ++ ; 
            }
        }

        // $timestamp = date('d-m-y H:i:s', strtotime($row2['timestamp']));
        // echo "<h1>" . $timestamp . "</h1>";
        ?>
    </div>

</section>


<!-- <?php include('templates/footer.php'); ?> -->

</html>