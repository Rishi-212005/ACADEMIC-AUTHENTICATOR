<?php
// This is a standalone OCR page that doesn't require authentication

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/ocr_images/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$extracted_text = "";
$error_message = "";
$success_message = "";
$image_path = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $file = $_FILES["image"];
    
    // Check for errors
    if ($file["error"] === 0) {
        $file_name = time() . "_" . basename($file["name"]);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
            // Check file size (limit to 5MB)
            if ($file["size"] <= 5000000) {
                // Allow certain file formats
                if (in_array($file_type, ["jpg", "jpeg", "png", "gif"])) {
                    if (move_uploaded_file($file["tmp_name"], $target_file)) {
                        $image_path = $target_file;
                        $success_message = "Image uploaded successfully.";
                        
                        // Call Python script for OCR
                        $python_script = "workers/standalone_ocr_extractor.py";
                        $command = "python $python_script \"$target_file\"";
                        $extracted_text = shell_exec($command);
                        
                        if (empty($extracted_text)) {
                            $error_message = "Failed to extract text. Please try again with a clearer image.";
                        }
                    } else {
                        $error_message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $error_message = "Sorry, your file is too large. Maximum size is 5MB.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    } else {
        $error_message = "Error uploading file. Error code: " . $file["error"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standalone OCR - Extract Text from Images</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .drop-zone:hover, .drop-zone.dragover {
            border-color: #4F46E5;
            background-color: rgba(79, 70, 229, 0.05);
        }
        .drop-zone__prompt {
            margin-bottom: 15px;
            font-weight: 500;
        }
        .drop-zone__input {
            display: none;
        }
        .drop-zone__thumb {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #cccccc;
            background-size: cover;
            background-position: center;
            position: relative;
            margin-top: 10px;
            display: none;
        }
        .drop-zone__thumb::after {
            content: attr(data-label);
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 5px 0;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.75);
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h1 class="text-2xl font-bold text-center text-indigo-700 mb-6">
                    <i class="fas fa-file-alt mr-2"></i>Image OCR - Extract Text from Images
                </h1>
                
                <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error_message; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?php echo $success_message; ?></p>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="mb-6">
                    <div class="drop-zone" id="drop-zone">
                        <div class="drop-zone__prompt">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p>Drag and drop an image file here or click to upload</p>
                            <p class="text-sm text-gray-500 mt-1">Supports: JPG, JPEG, PNG, GIF (Max: 5MB)</p>
                        </div>
                        <input type="file" name="image" class="drop-zone__input" id="fileInput" accept="image/*">
                        <div class="drop-zone__thumb" id="preview"></div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fas fa-magic mr-2"></i>Extract Text
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($extracted_text)): ?>
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-3 text-gray-800">
                        <i class="fas fa-file-alt mr-2"></i>Extracted Text
                    </h2>
                    <div class="relative">
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 text-gray-700 whitespace-pre-wrap" style="min-height: 150px;">
                            <?php echo nl2br(htmlspecialchars($extracted_text)); ?>
                        </div>
                        <button id="copyBtn" class="absolute top-2 right-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md p-2" title="Copy to clipboard">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    
                    <?php if (!empty($image_path)): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-2 text-gray-800">Uploaded Image</h3>
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <img src="<?php echo $image_path; ?>" alt="Uploaded Image" class="max-w-full h-auto">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('fileInput');
            const preview = document.getElementById('preview');
            const copyBtn = document.getElementById('copyBtn');
            
            // Handle drag and drop events
            ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });
            
            dropZone.addEventListener('dragover', function() {
                this.classList.add('dragover');
            }, false);
            
            dropZone.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            }, false);
            
            dropZone.addEventListener('drop', function(e) {
                this.classList.remove('dragover');
                fileInput.files = e.dataTransfer.files;
                updateThumbnail(e.dataTransfer.files[0]);
            }, false);
            
            dropZone.addEventListener('click', function() {
                fileInput.click();
            }, false);
            
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length) {
                    updateThumbnail(fileInput.files[0]);
                }
            });
            
            function updateThumbnail(file) {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function() {
                    preview.style.backgroundImage = `url('${reader.result}')`;
                    preview.style.display = 'block';
                    preview.setAttribute('data-label', file.name);
                };
            }
            
            // Copy to clipboard functionality
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    const textToCopy = document.querySelector('.bg-gray-50').innerText;
                    navigator.clipboard.writeText(textToCopy).then(function() {
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                        copyBtn.classList.remove('bg-gray-200', 'hover:bg-gray-300');
                        copyBtn.classList.add('bg-green-200', 'hover:bg-green-300');
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('bg-green-200', 'hover:bg-green-300');
                            copyBtn.classList.add('bg-gray-200', 'hover:bg-gray-300');
                        }, 2000);
                    });
                });
            }
        });
    </script>
</body>
</html>