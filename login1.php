<?php
INCLUDE 'config.php';

if (isset($_POST['login'])){
    $usename =$_POST['usename'];
    $pasword =$_POST['password'];
    $login = mysqli_query($koneksi, "select * from tb_user where username='$username' and pasword='$pasword'");
    if (mysqli_num_rows($login) > 0){
        $data = mysqli_fetch_assoc($login);
        if (data['role'] == 'admin'){
            header('location: admin/index.php');
        }else if (data['role'] == 'pelanggan'){
            header('location: index.php');
        }
    } else {
        echo "<script>alert('Username atau password salah')</script>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        username: <input type="text" name"username">
        <br><br>
        password: <input type="password" name="password">
        <input type="submit" name = "login">
</form>
    
</body>
</html>