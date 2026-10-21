<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Santri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="images/logofix1.png" type="image/x-icon">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success mb-4">
  <div class="container">
    <div class="d-flex align-items-center gap-2">
      <img src="images/logofix1.png" alt="Logo" width="40" height="40">
      <span class="navbar-text text-white fw-bold">Rumah Gemilang Indonesia</span>
    </div>
    <a class="navbar-brand" href="index.php">Data Santri</a>
  </div>
</nav>

<div class="container">
<?php if (isset($_SESSION['pesan'])): ?>
  <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
    <?= e($_SESSION['pesan']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
<?php endif; ?>