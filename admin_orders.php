<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['update_order'])){

   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'Thanh toán đã được cập nhật';

};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đặt hàng</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Đơn hàng chờ xử lý</h1>

   <div class="box-container">

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
          <div class="box" style="<?php if($fetch_orders['payment_status'] == 'completed'){ echo 'display:none'; }; ?>">
         <p> user id: <span><?= $fetch_orders['user_id']; ?></span> </p>
         <p> Thời gian đặt: <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> Tên: <span><?= $fetch_orders['name']; ?></span> </p>
         <p> Email: <span><?= $fetch_orders['email']; ?></span> </p>
         <p> Số điện thoại: <span><?= $fetch_orders['number']; ?></span> </p>
         <p> Địa chỉ: <span><?= $fetch_orders['address']; ?></span> </p>
         <p> Tổng đơn hàng: <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> Thành tiền: <span><?= $fetch_orders['total_price']; ?> VND</span> </p>
         <p> Phương thức thanh toán: <span><?= $fetch_orders['method']; ?></span> </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
               <option value="pending">pending</option>
               <option value="completed">completed</option>
            </select>
            <div class="flex-btn">
               <input type="submit" name="update_order" class="option-btn" value="Cập nhật">
               <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Xóa đơn hàng này?');">Xóa</a>
            </div>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Chưa có đơn hàng nào!</p>';
      }
      ?>

   </div>

</section>












<script src="js/script.js"></script>

</body>
</html>