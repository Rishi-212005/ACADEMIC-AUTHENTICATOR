<?php
session_start();
if (empty($_SESSION['user_id']) || (($_SESSION['user_type'] ?? '') !== 'institution')) {
    header('Location: login.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document Upload - Certificate Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" />
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Upload Document for Verification</h1>
        
        <div class="bg-white shadow rounded p-6 mb-6">
            <h2 class="text-xl font-medium mb-4">Upload Certificate Image</h2>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-4" id="dropZone">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">Drag & drop your image here</p>
                    <p class="text-xs text-gray-500 mb-4">Supported formats: PNG, JPG, JPEG (Max 10MB)</p>
                    <input type="file" id="fileInput" accept="image/*" class="hidden">
                    <button id="browseBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Browse Files</button>
                </div>
            </div>
            <div id="filePreview" class="hidden mb-4">
                <div class="flex items-center p-3 bg-gray-100 rounded">
                    <div class="mr-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p id="fileName" class="text-sm font-medium"></p>
                        <p id="fileSize" class="text-xs text-gray-500"></p>
                    </div>
                    <button id="removeFile" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <button id="extractBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition w-full">Extract Text</button>
        </div>
        
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-medium mb-4">Extracted Text</h2>
            <div id="loadingIndicator" class="hidden">
                <div class="flex items-center justify-center py-4">
                    <svg class="animate-spin h-6 w-6 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Processing image...</span>
                </div>
            </div>
            <div id="confidenceIndicator" class="hidden mb-2 text-sm">
                <span class="font-medium">Confidence:</span> <span id="confidenceValue">0%</span>
            </div>
            <pre id="extractedText" class="mt-2 p-4 bg-gray-900 text-green-200 whitespace-pre-wrap rounded overflow-auto" style="min-height:200px; max-height:400px"></pre>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const browseBtn = document.getElementById('browseBtn');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeFile = document.getElementById('removeFile');
            const extractBtn = document.getElementById('extractBtn');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const confidenceIndicator = document.getElementById('confidenceIndicator');
            const confidenceValue = document.getElementById('confidenceValue');
            const extractedText = document.getElementById('extractedText');
            
            // Handle browse button click
            browseBtn.addEventListener('click', () => {
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', handleFileSelect);
            
            // Handle drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500');
            });
            
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500');
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500');
                
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect();
                }
            });
            
            // Handle remove file button
            removeFile.addEventListener('click', () => {
                fileInput.value = '';
                filePreview.classList.add('hidden');
                dropZone.classList.remove('hidden');
                extractedText.textContent = '';
                confidenceIndicator.classList.add('hidden');
            });
            
            // Handle extract button click
            extractBtn.addEventListener('click', async () => {
                if (!fileInput.files.length) {
                    alert('Please select an image file first.');
                    return;
                }
                
                const file = fileInput.files[0];
                const formData = new FormData();
                formData.append('file', file);
                
                // Show loading indicator
                loadingIndicator.classList.remove('hidden');
                extractedText.textContent = '';
                confidenceIndicator.classList.add('hidden');
                
                try {
                    const response = await fetch('api.php?action=ocr_text', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    // Hide loading indicator
                    loadingIndicator.classList.add('hidden');
                    
                    if (result.error) {
                        extractedText.textContent = `Error: ${result.error}`;
                        return;
                    }
                    
                    // Display confidence
                    confidenceIndicator.classList.remove('hidden');
                    confidenceValue.textContent = `${Math.round(result.confidence * 100)}%`;
                    
                    // Display extracted text
                    extractedText.textContent = result.text || 'No text was extracted from the image.';
                    
                } catch (error) {
                    loadingIndicator.classList.add('hidden');
                    extractedText.textContent = `Error: ${error.message}`;
                }
            });
            
            // Helper function to handle file selection
            function handleFileSelect() {
                if (fileInput.files.length) {
                    const file = fileInput.files[0];
                    
                    // Check file type
                    const fileType = file.type;
                    if (!fileType.match('image.*')) {
                        alert('Please select an image file.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Check file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File size exceeds 10MB limit.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Update file preview
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    filePreview.classList.remove('hidden');
                    dropZone.classList.add('hidden');
                }
            }
            
            // Helper function to format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        });
    </script>
</body>
</html>