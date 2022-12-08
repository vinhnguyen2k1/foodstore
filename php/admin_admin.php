<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   header('location:admin_admin.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tài khoản</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">Tài khoản Admin</h1>

   <div class="box-container">

      <?php
         $select_users = $conn->prepare("SELECT * FROM `users`");
         $select_users->execute();
         while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box" style="<?php if($fetch_users['user_type'] == 'user'){ echo 'display:none'; }; ?>">
         <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="">
         <p> id: <span><?= $fetch_users['id']; ?></span></p>
         <p> Tên tài khoản: <span><?= $fetch_users['name']; ?></span></p>
         <p> Eamil: <span><?= $fetch_users['email']; ?></span></p>
         <p> Loại tài khoản: <span style=" color:<?php if($fetch_users['user_type'] == 'admin'){ echo 'orange'; }; ?>"><?= $fetch_users['user_type']; ?></span></p>
         <a href="admin_admin.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Xóa tài khoản này?');" class="delete-btn">Xóa</a>
      </div>
      <?php
      }
      ?>
   </div>

</section>



<script src="js/script.js"></script>

</body>
</html>