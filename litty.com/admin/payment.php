<?php
session_start();
include '../database/connect.php';
?>

<?php include 'header.php';?>

<style>
    .breadcrumb {
        margin-top: 7px;
    }
</style>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Thanh toán</h1>
            <div class="d-flex justify-content-between align-items-center">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                        <li class="breadcrumb-item active">Thanh toán</li>
                    </ol>
                </nav>
            </div>
        </div>
        
    </main>

<?php include 'footer.php'; ?>