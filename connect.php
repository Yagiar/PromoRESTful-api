<?php
$connect = mysqli_connect("localhost","root","","MyRaffle");
if (!$connect) {
    die("Ошибка подключения: " . mysqli_connect_error());
}