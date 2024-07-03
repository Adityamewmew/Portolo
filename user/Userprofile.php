<?php
require '../connect.php';

// Memulai transaksi
$conn->autocommit(FALSE);

try {
    // Menangkap data yang diupload
    if (isset($_POST['submit'])) {
        $media = $_FILES['media'];

        // Validasi upload file
        if ($media['error'] === 0) {
            // Mendapatkan ekstensi file
            $fileExt = pathinfo($media['name'], PATHINFO_EXTENSION);
            // Ekstensi yang diperbolehkan (sesuaikan jika diperlukan)
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'mp4'];

            // Memeriksa ekstensi yang diperbolehkan
            if (in_array(strtolower($fileExt), $allowedExtensions)) {
                // Generate nama unik untuk file
                $fileName = uniqid('', true) . '.' . $fileExt;
                $uploadPath = '../uploads' . $fileName; // Perubahan path untuk menyimpan file

                // Memindahkan file yang diupload ke folder uploads
                if (move_uploaded_file($media['tmp_name'], $uploadPath)) {
                    // Query untuk menyimpan informasi media ke database
                    $sqlMedia = "INSERT INTO media (type, url, upload_date) VALUES (?, ?, ?)";
                    $stmtMedia = $conn->prepare($sqlMedia);

                    // Binding parameter
                    $mediaType = $_POST['mediaType']; // Ambil jenis media dari form
                    $uploadDate = date('Y-m-d');
                    $stmtMedia->bind_param("sss", $mediaType, $uploadPath, $uploadDate);
                    $stmtMedia->execute();
                    $mediaId = $conn->insert_id;

                    // Simpan informasi ke tabel upload
                    $sqlUpload = "INSERT INTO uploads (media_id, upload_date) VALUES (?, ?)";
                    $stmtUpload = $conn->prepare($sqlUpload);
                    $stmtUpload->bind_param("is", $mediaId, $uploadDate);
                    $stmtUpload->execute();

                    // Simpan informasi kategori (jika ada)
                    if (!empty($_POST['tags'])) {
                        $kategori = $_POST['tags'];
                        $sqlKategori = "INSERT INTO kategori (kategori) VALUES (?)";
                        $stmtKategori = $conn->prepare($sqlKategori);
                        $stmtKategori->bind_param("s", $kategori);
                        $stmtKategori->execute();
                    }

                    // Simpan informasi ke tabel media_data
                    $fileSize = $media['size'];
                    $sqlMediaData = "INSERT INTO media_data (size) VALUES (?)";
                    $stmtMediaData = $conn->prepare($sqlMediaData);
                    $stmtMediaData->bind_param("i", $fileSize);
                    $stmtMediaData->execute();

                    // Commit transaksi
                    $conn->commit();
                    echo "File berhasil diunggah!";
                } else {
                    throw new Exception("Gagal mengunggah file.");
                }
            } else {
                throw new Exception("Jenis file tidak valid.");
            }
        } else {
            throw new Exception("Error upload: " . $media['error']);
        }
    }
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

// Mengembalikan autocommit ke mode default
$conn->autocommit(TRUE);

// Menutup koneksi database
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/style3.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-tranparant" style="background-color: #282D34;">
    <div class="container">
        <a class="navbar-brand" href="#">PortoLo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ">
                <li class="nav-item mx-2">
                    <a class="nav-link active" aria-current="page" href="../beranda/beranda.html">Home</a>
                </li>
                <li class="nav-item mx-2 dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownImage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Image
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownImage">
                        <li><a class="dropdown-item" href="../beranda/desain.html">Desain</a></li>
                        <li><a class="dropdown-item" href="../beranda/ilustrasi.html">Ilustrasi</a></li>
                    </ul>
                </li>
                <li class="nav-item mx-2 dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownVideo" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Video
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownVideo">
                        <li><a class="dropdown-item" href="../beranda/pemandangan.html">Pemandangan</a></li>
                        <li><a class="dropdown-item" href="../beranda/animasi.html">Animasi</a></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex mx-auto">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" style="width: 600px;">
                <button class="btn btn-outline-success" type="submit" >Search</button>
                <a href="../user/Userprofile.php">
                    <img  src="../img/website_-_male_user-512.webp"  alt="Avatar" style="width: 40px;  height: 40px; border-radius: 50%; margin-left: 10px;">
                </a>
            </form>
        </div>
    </div>
</nav>
<div class="padding">
    <div class="col-md-8">
        <!-- Column -->
        <div class="card">
            <img class="card-img-top" src="../img/adventure.jpg" alt="Card image cap">
            <div class="card-body little-profile text-center">
                <div class="pro-img"><img src="../img/dea1.jpg" alt="user"></div>
                <h3 class="m-b-0">Brad Macullam</h3>
                <p>Web Designer &amp; Developer</p>
                <a href="#" class="m-t-10 waves-effect waves-dark btn btn-primary btn-md btn-rounded" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload</a>
            </div>
        </div>
    </div>
</div>
<!-- Gallery Section -->
<div class="container">
    <div class="gallery" id="gallery"></div>
</div>
<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Unggah Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="upload-form" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="mediaType" class="form-label">Jenis Media</label>
                        <select class="form-select" id="mediaType" name="mediaType" required>
                            <option selected disabled>Pilih Jenis Media</option>
                            <option value="photo">Foto</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tag atau Kategori</label>
                        <select class="form-select" id="tags" name="tags">
                            <option selected disabled>Pilih Tag atau Kategori</option>
                            <option value="animation">Animasi</option>
                            <option value="landscape">Pemandangan</option>
                            <option value="landscape">Desain</option>
                            <option value="landscape">Ilustrasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="media" class="form-label">Pilih File</label>
                        <input type="file" class="form-control" id="media" name="media" required>
                    </div>
                    <input type="hidden" name="user_id" value="1"> <!-- Sesuaikan user_id dengan dinamis -->
                    <button type="submit" name="submit" class="btn btn-primary">Unggah</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>



function fetchMedia() {
    fetch('../fetchmedia.php')
        .then(response => response.json())
        .then(data => {
            const gallery = document.getElementById('gallery');
            let html = '';
            data.forEach(media => {
                if (media.type === 'photo') {
                    html += `<img src="${media.url}" alt="photo" class="img-fluid">`;
                } else if (media.type === 'video') {
                    html += `<video controls><source src="${media.url}" type="video/mp4"></video>`;
                }
            });
            gallery.innerHTML = html;
        })
        .catch(error => console.error('Error fetching media:', error));
}

fetchMedia();


function loadUploads() {
        const uploads = JSON.parse(localStorage.getItem('uploads')) || [];
        const gallery = document.getElementById('gallery');
        gallery.innerHTML = '';
    
        uploads.forEach((upload, index) => {
            const galleryItem = document.createElement('div');
            galleryItem.classList.add('gallery-item');
            galleryItem.dataset.index = index;
    
            if (upload.type === 'photo') {
                const img = document.createElement('img');
                img.src = upload.src;
                galleryItem.appendChild(img);
            } else if (upload.type === 'video') {
                const video = document.createElement('video');
                video.controls = true;
                const source = document.createElement('source');
                source.src = upload.src;
                source.type = 'video/mp4';
                video.appendChild(source);
                galleryItem.appendChild(video);
            }
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Hapus';
            deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'delete-button');
            galleryItem.appendChild(deleteButton);
    
            gallery.appendChild(galleryItem);
        });
    }
    
        document.addEventListener('DOMContentLoaded', function() {
          const uploadForm = document.getElementById('upload-form');
           uploadForm.addEventListener('submit', function(event) {
            event.preventDefault();
    
            const mediaType = document.getElementById('mediaType').value;
            const mediaInput = document.getElementById('media');
            const file = mediaInput.files[0];
    
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const uploads = JSON.parse(localStorage.getItem('uploads')) || [];
                    uploads.push({
                        type: mediaType,
                        src: e.target.result
                    });
                    localStorage.setItem('uploads', JSON.stringify(uploads));
                    loadUploads();
                    // Reload the home page gallery
                    if (window.opener) {
                        window.opener.loadUploads();
                    }
                    mediaInput.value = '';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                    modal.hide();
                };
                reader.readAsDataURL(file);
            }
        });
    
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-button')) {
                const index = parseInt(event.target.parentElement.dataset.index);
                const uploads = JSON.parse(localStorage.getItem('uploads')) || [];
                uploads.splice(index, 1);
                localStorage.setItem('uploads', JSON.stringify(uploads));
                loadUploads();
                if (window.opener) {
                    window.opener.loadUploads();
                }
            }
        });
    });


</script>
</body>
</html>
