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
    <title>Upload Document - OCR Extraction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 20px;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 25px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 28px;
            color: #3498db;
        }
        
        .logo-text {
            font-weight: 600;
            font-size: 22px;
        }
        
        .status {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            background: #2ecc71;
            border-radius: 50%;
        }
        
        .content {
            padding: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #3498db;
        }
        
        .details-grid {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: #3498db;
            font-size: 14px;
        }
        
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .field-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .field-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .field-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        
        .field-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
            word-break: break-word;
        }
        
        .full-width-field {
            grid-column: 1 / span 2;
        }
        
        .achievement-section {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #2196f3;
            margin-top: 10px;
        }
        
        .achievement-title {
            font-size: 14px;
            color: #1565c0;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .achievement-value {
            font-size: 18px;
            color: #0d47a1;
            font-weight: 700;
        }
        
        .result-section {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #4caf50;
            margin-top: 10px;
        }
        
        .result-title {
            font-size: 14px;
            color: #2e7d32;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .result-value {
            font-size: 18px;
            color: #1b5e20;
            font-weight: 700;
        }
        
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        
        .btn-outline:hover {
            background: #f8f9fa;
            color: #495057;
        }
        
        .btn-upload {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .btn-upload:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        
        .loader {
            border-top-color: #3498db;
            -webkit-animation: spinner 1.5s linear infinite;
            animation: spinner 1.5s linear infinite;
        }
        
        @-webkit-keyframes spinner {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .upload-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .upload-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        
        .upload-content {
            padding: 30px;
        }
        
        @media (max-width: 768px) {
            .fields-grid {
                grid-template-columns: 1fr;
            }
            
            .full-width-field {
                grid-column: 1;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="upload-section">
        <div class="upload-header">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-file-upload"></i></div>
                <div class="logo-text">Document OCR Processor</div>
            </div>
        </div>
        <div class="upload-content">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Upload Image/PDF for Text Extraction</h1>
                <a href="institute_dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-4" id="dropZone">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">Drag & drop your file here</p>
                    <p class="text-xs text-gray-500 mb-4">Supported: PNG, JPG, JPEG, PDF (Max 10MB)</p>
                    <input type="file" id="fileInput" accept="image/*,.pdf" class="hidden">
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
            <div class="flex justify-center">
                <button id="extractBtn" class="btn btn-upload">
                    <i class="fas fa-magic"></i>
                    Show Details
                </button>
            </div>
        </div>
        
        <div id="loadingSection" class="hidden bg-white shadow rounded p-6 mb-6">
            <div class="flex items-center justify-center">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
            </div>
            <p class="text-center text-gray-600">Processing image, please wait...</p>
        </div>
        
        <div id="resultSection" class="hidden">
            <div class="container">
                <div class="header">
                    <div class="logo">
                        <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                        <div class="logo-text">Academic Credentials Portal</div>
                    </div>
                    <div class="status">
                        <div class="status-dot"></div>
                        <span id="confidenceText">Confidence: <span id="confidenceValue">0%</span></span>
                    </div>
                </div>
                
                <div class="content">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        Student Information
                    </div>
                    
                    <div class="details-grid">
                        <div id="structuredBody"></div>
                    </div>
                    
                    <div class="action-buttons">
                        <button id="newImageBtn" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Process New File
                        </button>
                        <button id="copyBtn" class="btn btn-primary">
                            <i class="fas fa-copy"></i>
                            Copy Details
                        </button>
                    </div>
                </div>
                
                <div class="footer">
                    <p>© 2025 Academic Credentials Portal | Secured & Verified</p>
                </div>
            </div>
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
            const loadingSection = document.getElementById('loadingSection');
            const resultSection = document.getElementById('resultSection');
            // extractedText element removed - no longer needed
            const confidenceValue = document.getElementById('confidenceValue');
            const copyBtn = document.getElementById('copyBtn');
            const newImageBtn = document.getElementById('newImageBtn');
            
            let selectedFile = null;
            
            // Handle file browse button
            browseBtn.addEventListener('click', () => {
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });
            
            // Handle drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            }
            
            function unhighlight() {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            }
            
            dropZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }
            
            function handleFiles(files) {
                if (files.length > 0) {
                    selectedFile = files[0];
                    const isImage = selectedFile.type.match('image.*');
                    const isPdf = selectedFile.type === 'application/pdf' || selectedFile.name.toLowerCase().endsWith('.pdf');
                    if (!isImage && !isPdf) {
                        alert('Please select an image or PDF file');
                        return;
                    }
                    
                    // Check file size (max 10MB)
                    if (selectedFile.size > 10 * 1024 * 1024) {
                        alert('File size exceeds 10MB limit');
                        return;
                    }
                    
                    // Update file preview
                    fileName.textContent = selectedFile.name;
                    fileSize.textContent = formatFileSize(selectedFile.size);
                    filePreview.classList.remove('hidden');
                }
            }
            
            function formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' bytes';
                else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                else return (bytes / 1048576).toFixed(1) + ' MB';
            }
            
            // Remove selected file
            removeFile.addEventListener('click', () => {
                selectedFile = null;
                fileInput.value = '';
                filePreview.classList.add('hidden');
            });
            
            // Extract text from file
            extractBtn.addEventListener('click', () => {
                if (!selectedFile) {
                    alert('Please select a file first');
                    return;
                }
                
                // Show loading section
                loadingSection.classList.remove('hidden');
                resultSection.classList.add('hidden');
                
                // Create form data
                const formData = new FormData();
                formData.append('file', selectedFile);
                
                // Send request to API
                fetch('api.php?action=ocr_text', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
               // In your extractBtn click handler, remove the auto-save line:
.then(data => {
    // Hide loading section
    loadingSection.classList.add('hidden');
    
    if (data.error) {
        alert('Error: ' + (data.detail || data.error));
        return;
    }
    
    // Show result section
    resultSection.classList.remove('hidden');
    
    // Update confidence
    const confidence = Math.round((data.confidence || 0) * 100);
    confidenceValue.textContent = confidence + '%';
    
    window.currentExtractedFields = data.extracted_fields || {};
    
    // Build structured details
    if (data.extracted_fields) {
        console.log('Extracted fields:', data.extracted_fields);
        buildStructuredDetailsFromFields(data.extracted_fields);
    } else {
        buildStructuredDetails(data.text || 'No text found in the file');
    }
    
    // Update confidence color
    if (confidence >= 70) {
        confidenceValue.className = 'px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium';
    } else if (confidence >= 40) {
        confidenceValue.className = 'px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-medium';
    } else {
        confidenceValue.className = 'px-2 py-1 bg-red-100 text-red-800 rounded text-sm font-medium';
    }

    // REMOVE THIS LINE - No automatic saving
    // if (selectedFile) {
    //     saveOcrDataToDatabase(data, selectedFile.name);
    // }
})
                .catch(error => {
                    loadingSection.classList.add('hidden');
                    alert('Error: ' + error.message);
                });
            });

            // Add a new button to push data to database manually
            const pushToDbBtn = document.createElement('button');
            pushToDbBtn.textContent = 'Save to Database';
            pushToDbBtn.className = 'btn btn-primary mt-4';
            pushToDbBtn.style.marginLeft = '10px';

            // Append the button to the action-buttons div
            const actionButtonsDiv = document.querySelector('.action-buttons');
            if (actionButtonsDiv) {
                actionButtonsDiv.appendChild(pushToDbBtn);
            }

            // Add click event to push data to database
            // Replace your current pushToDbBtn event listener with this:

// Add click event to push data to database
pushToDbBtn.addEventListener('click', () => {
    if (!window.currentExtractedFields) {
        alert('No extracted data available to save.');
        return;
    }
    
    if (!selectedFile) {
        alert('No file selected. Please upload a file first.');
        return;
    }
    
    // Show loading state on the button
    const originalText = pushToDbBtn.innerHTML;
    pushToDbBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    pushToDbBtn.disabled = true;
    
    // Prepare data object similar to OCR API response
    const dataToSave = {
        extracted_fields: window.currentExtractedFields,
        confidence: parseFloat(confidenceValue.textContent) / 100 || 0.6,
        file_name: selectedFile.name
    };
    
    console.log('Saving to database:', dataToSave);
    
    // Call the save function
    saveOcrDataToDatabase(dataToSave, selectedFile.name)
        .then(() => {
            // Refresh the page after successful save
            window.location.reload();
        })
        .catch((err) => {
            alert('Failed to save: ' + (err && err.message ? err.message : err));
        })
        .finally(() => {
            // Restore button state
            pushToDbBtn.innerHTML = originalText;
            pushToDbBtn.disabled = false;
        });
});            
            // Copy extracted text
            copyBtn.addEventListener('click', () => {
                // Get all the structured data as text
                const structuredData = document.getElementById('structuredBody');
                if (!structuredData) {
                    alert('No data to copy');
                    return;
                }
                
                let copyText = 'Certificate Details\n\n';
                const sections = structuredData.querySelectorAll('.section');
                sections.forEach(section => {
                    const sectionTitle = section.querySelector('.section-title').textContent.trim();
                    copyText += `${sectionTitle}:\n`;
                    
                    const fields = section.querySelectorAll('.field-item');
                    fields.forEach(field => {
                        const label = field.querySelector('.field-label').textContent;
                        const valueElement = field.querySelector('.field-value');
                        const value = valueElement ? valueElement.textContent : '';
                        copyText += `  ${label}: ${value}\n`;
                    });
                    copyText += '\n';
                });
                
                // Add achievement section if present (now part of Academic Performance)
                // Add result section if present
                const resultSectionElement = structuredData.querySelector('.result-section');
                if (resultSectionElement) {
                    const title = resultSectionElement.querySelector('.result-title').textContent;
                    const value = resultSectionElement.querySelector('.result-value').textContent;
                    copyText += `${title}: ${value}\n`;
                    
                    // Add reasoning if available as tooltip
                    const reasoning = resultSectionElement.title;
                    if (reasoning) {
                        copyText += `Reasoning: ${reasoning.replace('Reasoning: ', '')}\n`;
                    }
                }
                
                navigator.clipboard.writeText(copyText)
                    .then(() => {
                        alert('Certificate details copied to clipboard');
                    })
                    .catch(err => {
                        alert('Failed to copy text: ' + err);
                    });
            });
            
            // No need for format buttons anymore

            // Data validation and quality improvement function
            function validateAndImproveData(data, text) {
                const validatedData = { ...data };
                
                // Validate and improve Student Name
                if (validatedData.StudentName) {
                    // Remove common OCR artifacts from names
                    validatedData.StudentName = validatedData.StudentName
                        .replace(/\s+[a-z]{1,2}\s*$/i, '') // Remove trailing 1-2 letter artifacts
                        .replace(/\s+[a-z]{1,2}\s+/i, ' ') // Remove middle 1-2 letter artifacts
                        .replace(/\s+/g, ' ') // Clean up spaces
                        .trim();
                }
                
                // Validate and improve University Name
                if (validatedData.UniversityName) {
                    // Ensure university name is complete
                    if (validatedData.UniversityName.length < 10) {
                        // Look for more complete university name
                        const uniMatch = text.match(/([A-Z\s]*UNIVERSITY[A-Z\s]*)/i);
                        if (uniMatch && uniMatch[1].length > validatedData.UniversityName.length) {
                            validatedData.UniversityName = uniMatch[1].trim();
                        }
                    }
                }
                
                // Validate and improve College Name
                if (validatedData.CollegeName) {
                    // Ensure college name is complete
                    if (validatedData.CollegeName.length < 5) {
                        // Look for more complete college name
                        const colMatch = text.match(/(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/i);
                        if (colMatch && colMatch[1].length > validatedData.CollegeName.length) {
                            validatedData.CollegeName = colMatch[1].trim();
                        }
                    }
                }
                
                // Validate Hall Ticket Number format
                if (validatedData.HallTicketNo) {
                    // Ensure it follows the correct format
                    const htPattern = /^(\d{5}[A-Z0-9]\d{4})$/;
                    if (!htPattern.test(validatedData.HallTicketNo.replace(/\s/g, ''))) {
                        // Try to find a better match
                        const htMatch = text.match(/\b(\d{5}[A-Z0-9]\d{4})\b/);
                        if (htMatch) {
                            validatedData.HallTicketNo = htMatch[1];
                        }
                    }
                }
                
                // Validate Roll Number format
                if (validatedData.RollNumber) {
                    // Ensure it follows the correct format
                    const rollPattern = /^(\d{5}[A-Z0-9]\d{4})$/;
                    if (!rollPattern.test(validatedData.RollNumber.replace(/\s/g, ''))) {
                        // Try to find a better match
                        const rollMatch = text.match(/\b(\d{5}[A-Z0-9]\d{4})\b/);
                        if (rollMatch) {
                            validatedData.RollNumber = rollMatch[1];
                        }
                    }
                }
                
                // Validate CGPA/SGPA values
                if (validatedData.CGPA) {
                    const cgpa = parseFloat(validatedData.CGPA);
                    if (isNaN(cgpa) || cgpa < 0 || cgpa > 10) {
                        // Try to find a better match
                        const cgpaMatch = text.match(/(CGPA|C\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i);
                        if (cgpaMatch) {
                            const newCgpa = parseFloat(cgpaMatch[2]);
                            if (!isNaN(newCgpa) && newCgpa >= 0 && newCgpa <= 10) {
                                validatedData.CGPA = cgpaMatch[2];
                            }
                        }
                    }
                }
                
                if (validatedData.SGPA) {
                    const sgpa = parseFloat(validatedData.SGPA);
                    if (isNaN(sgpa) || sgpa < 0 || sgpa > 10) {
                        // Try to find a better match
                        const sgpaMatch = text.match(/(SGPA|S\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i);
                        if (sgpaMatch) {
                            const newSgpa = parseFloat(sgpaMatch[2]);
                            if (!isNaN(newSgpa) && newSgpa >= 0 && newSgpa <= 10) {
                                validatedData.SGPA = sgpaMatch[2];
                            }
                        }
                    }
                }
                
                return validatedData;
            }

            // Intelligent Pass/Fail Detection Function
            function detectPassFailStatus(text, data) {
                const upper = text.toUpperCase();
                let passScore = 0;
                let failScore = 0;
                let confidence = 0;
                
                // Direct indicators
                if (/PASSED|PASS|SUCCESSFUL|COMPLETED|QUALIFIED/i.test(text)) {
                    passScore += 10;
                }
                if (/FAILED|FAIL|UNSUCCESSFUL|DISQUALIFIED|REJECTED/i.test(text)) {
                    failScore += 10;
                }
                
                // Grade-based indicators
                const gradePatterns = {
                    // High grades (likely pass)
                    'DISTINCTION': 8,
                    'FIRST CLASS': 7,
                    'SECOND CLASS': 6,
                    'THIRD CLASS': 5,
                    'HONOURS': 6,
                    'MERIT': 5,
                    'EXCELLENT': 7,
                    'VERY GOOD': 6,
                    'GOOD': 5,
                    'SATISFACTORY': 4,
                    
                    // Low grades (likely fail)
                    'POOR': -3,
                    'UNSATISFACTORY': -5,
                    'INCOMPLETE': -4,
                    'INSUFFICIENT': -4,
                    'BELOW AVERAGE': -3
                };
                
                for (const [pattern, score] of Object.entries(gradePatterns)) {
                    const regex = new RegExp(pattern, 'gi');
                    const matches = text.match(regex);
                    if (matches) {
                        if (score > 0) {
                            passScore += score * matches.length;
                        } else {
                            failScore += Math.abs(score) * matches.length;
                        }
                    }
                }
                
                // CGPA/SGPA analysis
                if (data.CGPA) {
                    const cgpa = parseFloat(data.CGPA);
                    if (cgpa >= 7.0) {
                        passScore += 8;
                    } else if (cgpa >= 6.0) {
                        passScore += 6;
                    } else if (cgpa >= 5.0) {
                        passScore += 4;
                    } else if (cgpa >= 4.0) {
                        passScore += 2;
                    } else if (cgpa < 4.0) {
                        failScore += 6;
                    }
                }
                
                if (data.SGPA) {
                    const sgpa = parseFloat(data.SGPA);
                    if (sgpa >= 7.0) {
                        passScore += 6;
                    } else if (sgpa >= 6.0) {
                        passScore += 4;
                    } else if (sgpa >= 5.0) {
                        passScore += 3;
                    } else if (sgpa >= 4.0) {
                        passScore += 1;
                    } else if (sgpa < 4.0) {
                        failScore += 4;
                    }
                }
                
                // Percentage analysis
                const percentageMatch = text.match(/(\d+(?:\.\d+)?)\s*%/gi);
                if (percentageMatch) {
                    for (const match of percentageMatch) {
                        const percentage = parseFloat(match);
                        if (percentage >= 70) {
                            passScore += 5;
                        } else if (percentage >= 60) {
                            passScore += 3;
                        } else if (percentage >= 50) {
                            passScore += 1;
                        } else if (percentage < 50) {
                            failScore += 4;
                        }
                    }
                }
                
                // Certificate/Transcript indicators
                if (/CERTIFICATE|DEGREE|DIPLOMA|TRANSCRIPT|MARK SHEET/i.test(text)) {
                    passScore += 3; // Having a certificate usually means pass
                }
                
                // University/College completion indicators
                if (/COMPLETED|GRADUATED|AWARDED|CONFERRED/i.test(text)) {
                    passScore += 5;
                }
                
                // Year/Semester completion indicators
                if (/FINAL YEAR|FINAL SEMESTER|COMPLETED.*YEAR|COMPLETED.*SEMESTER/i.test(text)) {
                    passScore += 4;
                }
                
                // Subject-wise pass indicators
                if (/ALL SUBJECTS|ALL PAPERS|EACH SUBJECT|EVERY SUBJECT/i.test(text)) {
                    passScore += 3;
                }
                
                // Attendance indicators
                if (/ATTENDANCE.*SATISFACTORY|ATTENDANCE.*COMPLETE/i.test(text)) {
                    passScore += 2;
                }
                
                // Negative indicators
                if (/INCOMPLETE|PENDING|REPEAT|REAPPEAR|BACKLOG|ARREAR/i.test(text)) {
                    failScore += 6;
                }
                
                if (/DETAINED|SUSPENDED|TERMINATED/i.test(text)) {
                    failScore += 8;
                }
                
                // Date analysis - if issue date is present, likely passed
                if (data.IssueDate || data.CertificateSerialNo) {
                    passScore += 3;
                }
                
                // Calculate confidence and determine status
                const totalScore = passScore + failScore;
                if (totalScore === 0) {
                    return null; // No clear indication
                }
                
                confidence = Math.min(100, Math.abs(passScore - failScore) / totalScore * 100);
                
                if (passScore > failScore) {
                    return {
                        status: 'PASSED',
                        confidence: Math.round(confidence),
                        reasoning: generateReasoning(passScore, failScore, data)
                    };
                } else {
                    return {
                        status: 'FAILED',
                        confidence: Math.round(confidence),
                        reasoning: generateReasoning(passScore, failScore, data)
                    };
                }
            }
            
            // Generate reasoning for pass/fail decision
            function generateReasoning(passScore, failScore, data) {
                const reasons = [];
                
                if (data.CGPA && parseFloat(data.CGPA) >= 5.0) {
                    reasons.push(`CGPA: ${data.CGPA}`);
                }
                if (data.SGPA && parseFloat(data.SGPA) >= 5.0) {
                    reasons.push(`SGPA: ${data.SGPA}`);
                }
                if (data.AggregateInWords) {
                    reasons.push(`Grade: ${data.AggregateInWords}`);
                }
                if (data.IssueDate) {
                    reasons.push('Certificate issued');
                }
                
                return reasons.length > 0 ? reasons.join(', ') : 'Based on academic performance analysis';
            }

            function buildStructuredDetailsFromFields(extractedFields) {
                // Render organized sections from extracted fields
                const body = document.getElementById('structuredBody');
                body.innerHTML = '';
                
                const sections = {
                    'Student Details': {
                        icon: 'fas fa-user-graduate',
                        fields: [
                            { key: 'Student Name', label: 'Student Name' },
                            { key: 'Hall Ticket No', label: 'Hall Ticket No' },
                            { key: 'SerialNo', label: 'Serial No' }
                        ]
                    },
                    'University Information': {
                        icon: 'fas fa-university',
                        fields: [
                            { key: 'University Name', label: 'University Name' },
                            { key: 'College Name', label: 'College Name' },
                            { key: 'Branch', label: 'Branch' }
                        ]
                    },
                    'Examination Details': {
                        icon: 'fas fa-calendar-alt',
                        fields: [
                            { key: 'Examination Date', label: 'Examination Date' }
                        ]
                    },
                    'Academic Performance': {
                        icon: 'fas fa-chart-line',
                        fields: [
                            { key: 'CGPA', label: 'CGPA' },
                            { key: 'SGPA', label: 'SGPA' },
                            { key: 'Result', label: 'Result' },
                            { key: 'AggregateInWords', label: 'Aggregate Marks' }
                        ]
                    }
                };
                
                // Store current extracted fields globally for edit functionality
                window.currentExtractedFields = extractedFields;
                
                // Create sections
                for (const [sectionName, sectionData] of Object.entries(sections)) {
                    const availableFields = sectionData.fields; // Show all fields
                    
                    const sectionDiv = document.createElement('div');
                    sectionDiv.className = 'section';
                    
                    const sectionTitle = document.createElement('div');
                    sectionTitle.className = 'section-title';
                    sectionTitle.innerHTML = `<i class="${sectionData.icon}"></i>${sectionName}`;
                    sectionDiv.appendChild(sectionTitle);
                    
                    const fieldsGrid = document.createElement('div');
                    fieldsGrid.className = 'fields-grid';
                    
                    for (const field of availableFields) {
                        const fieldDiv = document.createElement('div');
                        fieldDiv.className = 'field-item';
                        
                        // Determine if this should be full width
                        const fullWidthFields = ['University Name', 'College Name', 'Branch'];
                        if (fullWidthFields.includes(field.key)) {
                            fieldDiv.classList.add('full-width-field');
                        }
                        
                        const fieldValue = extractedFields[field.key] || '';
                        const displayValue = fieldValue || 'Not Found';
                        
                        const fieldId = `field_${field.key.replace(/\s+/g, '_').replace(/[()]/g, '')}`;
                        
                        fieldDiv.innerHTML = `
                            <div class="field-label">${escapeHtml(field.label)}</div>
                            <div class="field-value-container" style="display: flex; align-items: center; gap: 8px;">
                                <div class="field-value" id="${fieldId}_display" style="flex: 1;">${escapeHtml(displayValue)}</div>
                                <input type="text" class="field-input" id="${fieldId}_input" value="${escapeHtml(fieldValue)}" style="display: none; flex: 1; padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                <button class="edit-btn" onclick="toggleEdit('${fieldId}')" style="background: #007bff; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        `;
                        
                        fieldsGrid.appendChild(fieldDiv);
                    }
                    
                    sectionDiv.appendChild(fieldsGrid);
                    body.appendChild(sectionDiv);
                }
                
                
                if (body.children.length === 0) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'section text-center';
                    emptyDiv.innerHTML = `
                        <div class="section-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            No Data Found
                        </div>
                        <div class="field-value">No structured fields detected. Please try with a different document.</div>
                    `;
                    body.appendChild(emptyDiv);
                }
            }

            window.toggleEdit = function(fieldId) {
                const displayElement = document.getElementById(fieldId + '_display');
                const inputElement = document.getElementById(fieldId + '_input');
                const button = document.querySelector(`button[onclick="toggleEdit('${fieldId}')"]`);
                
                if (inputElement.style.display === 'none') {
                    // Switch to edit mode
                    displayElement.style.display = 'none';
                    inputElement.style.display = 'block';
                    inputElement.focus();
                    button.innerHTML = '<i class="fas fa-save"></i> Save';
                    button.style.background = '#28a745';
                } else {
                    // Switch to display mode and save
                    const newValue = inputElement.value.trim();
                    displayElement.textContent = newValue || 'Not Found';
                    displayElement.style.display = 'block';
                    inputElement.style.display = 'none';
                    button.innerHTML = '<i class="fas fa-edit"></i> Edit';
                    button.style.background = '#007bff';
                    
                    // Update the extracted fields data
                    const fieldKey = fieldId.replace('field_', '').replace(/_/g, ' ').replace(/Scale 10/, '(Scale 10)');
                    if (window.currentExtractedFields) {
                        window.currentExtractedFields[fieldKey] = newValue;
                    }
                }
            };

            function buildStructuredDetails(text) {
                // Safety check for text parameter
                if (!text || typeof text !== 'string') {
                    text = String(text || '');
                }
                
                // Advanced text preprocessing for better OCR accuracy
                let processedText = text;
                
                // Fix common OCR errors before processing
                processedText = processedText
                    // Fix common character misreads
                    .replace(/[|]/g, 'I')  // Pipe to I
                    .replace(/[0O]/g, 'O')  // Zero to O
                    .replace(/[1l]/g, 'l')  // One to l
                    .replace(/[5S]/g, 'S')  // Five to S
                    .replace(/[8B]/g, 'B')  // Eight to B
                    .replace(/[6G]/g, 'G')  // Six to G
                    .replace(/[9g]/g, 'g')  // Nine to g
                    .replace(/[2Z]/g, 'Z')  // Two to Z
                    .replace(/[3E]/g, 'E')  // Three to E
                    .replace(/[4A]/g, 'A')  // Four to A
                    .replace(/[7T]/g, 'T')  // Seven to T
                    
                    // Fix spacing issues
                    .replace(/\s+/g, ' ')  // Multiple spaces to single
                    .replace(/\n\s+/g, '\n')  // Remove leading spaces from lines
                    .replace(/\s+\n/g, '\n')  // Remove trailing spaces from lines
                    
                    // Fix common OCR artifacts
                    .replace(/[^\w\s\.$$$$\-\/|:]/g, ' ')  // Remove special characters
                    .replace(/\s+/g, ' ')  // Clean up spaces
                    .trim();
                
                // Normalize
                const normalized = processedText.replace(/\t+/g, ' ').replace(/\u00A0/g, ' ').replace(/\s+\n/g, '\n').trim();
                const upper = normalized.toUpperCase();
                const lines = normalized.split(/\r?\n/).map(l => l.trim()).filter(l => l);
                
                // Auto-correct function to fix common OCR errors
                function autoCorrect(text) {
                    if (!text || typeof text !== 'string') return text || '';
                    
                    let corrected = text;
                    
                    // Month corrections
                    const monthCorrections = {
                        'DE!': 'DECEMBER',
                        'DE': 'DECEMBER',
                        'NOV': 'NOVEMBER',
                        'OCT': 'OCTOBER',
                        'SEP': 'SEPTEMBER',
                        'AUG': 'AUGUST',
                        'JUL': 'JULY',
                        'JULYY': 'JULY',
                        'JULY!': 'JULY',
                        'JUN': 'JUNE',
                        'JUNEE': 'JUNE',
                        'JUNE!': 'JUNE',
                        'MAY': 'MAY',
                        'APR': 'APRIL',
                        'MAR': 'MARCH',
                        'FEB': 'FEBRUARY',
                        'JAN': 'JANUARY'
                    };
                    
                    // Name corrections - remove common OCR artifacts
                    corrected = corrected.replace(/\s+ea\s*$/i, ''); // Remove trailing 'ea'
                    corrected = corrected.replace(/\s+ea\s+/i, ' '); // Remove middle 'ea'
                    corrected = corrected.replace(/\s+[a-z]{1,2}\s*$/i, ''); // Remove trailing 1-2 letter artifacts
                    corrected = corrected.replace(/\s+[a-z]{1,2}\s+/i, ' '); // Remove middle 1-2 letter artifacts
                    
                    // Hall Ticket Number specific corrections (A/4 confusion)
                    corrected = corrected.replace(/(\d{5})4(\d{4})/g, '$1A$2'); // Fix 4->A in hall ticket numbers
                    corrected = corrected.replace(/(\d{5})[O0](\d{4})/g, '$1A$2'); // Fix O/0->A in hall ticket numbers
                    corrected = corrected.replace(/(\d{5})[B8](\d{4})/g, '$1A$2'); // Fix B/8->A in hall ticket numbers
                    
                    // General text cleaning
                    corrected = corrected.replace(/\s+/g, ' '); // Multiple spaces to single space
                    corrected = corrected.replace(/\s*[|]\s*/g, ' | '); // Clean pipe separators
                    corrected = corrected.replace(/\s*[:\-]\s*/g, ': '); // Clean colons and dashes
                    corrected = corrected.replace(/\s*[!]+\s*/g, ''); // Remove exclamation marks
                    corrected = corrected.replace(/\s*[,]+\s*/g, ', '); // Clean commas
                    
                    // OCR Error Corrections for Roman Numerals and Common Misreads
                    corrected = corrected.replace(/\bB\.?\s*TECH\s*[|]\s*YEAR\s*II\s*SEMESTER/i, 'B.Tech II YEAR SEMESTER'); // Fix | as Roman I
                    corrected = corrected.replace(/\bB\.?\s*TECH\s*[|]\s*YEAR\s*I\s*SEMESTER/i, 'B.Tech I YEAR SEMESTER'); // Fix | as Roman I
                    corrected = corrected.replace(/\bB\.?\s*TECH\s*[|]\s*YEAR\s*III\s*SEMESTER/i, 'B.Tech III YEAR SEMESTER'); // Fix | as Roman I
                    corrected = corrected.replace(/\bB\.?\s*TECH\s*[|]\s*YEAR\s*IV\s*SEMESTER/i, 'B.Tech IV YEAR SEMESTER'); // Fix | as Roman I
                    
                    // Remove common OCR garbage text patterns
                    corrected = corrected.replace(/\s+uatiticxerxo\s*/i, ' '); // Remove OCR garbage
                    corrected = corrected.replace(/\s+nati\s+nickerno\s*/i, ' '); // Remove OCR garbage
                    corrected = corrected.replace(/\s+[a-z]{10,}\s*/g, ' '); // Remove long random strings (likely OCR errors)
                    corrected = corrected.replace(/\s+[a-z]{5,}[^a-z\s]\s*/g, ' '); // Remove mixed char strings
                    
                    // University/College name corrections
                    corrected = corrected.replace(/JAWAHARLAL\s+NEHRU\s+TECHNOLOGICAL\s+UNIVERSITY\s+ANANTAPUR\s+COLLEGE/i, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR COLLEGE');
                    corrected = corrected.replace(/JAWAHARLAL\s+NEHRU\s+TECHNOLOGICAL\s+UNIVERSITY\s+ANANTAPUR/i, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR');
                    
                    // Course corrections
                    corrected = corrected.replace(/B\.?\s*TECH/i, 'B.Tech');
                    corrected = corrected.replace(/M\.?\s*TECH/i, 'M.Tech');
                    corrected = corrected.replace(/B\.?\s*SC/i, 'B.Sc');
                    corrected = corrected.replace(/M\.?\s*SC/i, 'M.Sc');
                    corrected = corrected.replace(/B\.?\s*COM/i, 'B.Com');
                    corrected = corrected.replace(/M\.?\s*COM/i, 'M.Com');
                    
                    // Branch corrections
                    corrected = corrected.replace(/COMPUTER\s+SCIENCE\s+AND\s+ENGINEERING/i, 'COMPUTER SCIENCE AND ENGINEERING');
                    corrected = corrected.replace(/ELECTRONICS\s+AND\s+COMMUNICATION\s+ENGINEERING/i, 'ELECTRONICS AND COMMUNICATION ENGINEERING');
                    corrected = corrected.replace(/MECHANICAL\s+ENGINEERING/i, 'MECHANICAL ENGINEERING');
                    corrected = corrected.replace(/CIVIL\s+ENGINEERING/i, 'CIVIL ENGINEERING');
                    corrected = corrected.replace(/ELECTRICAL\s+AND\s+ELECTRONICS\s+ENGINEERING/i, 'ELECTRICAL AND ELECTRONICS ENGINEERING');
                    
                    // Apply month corrections
                    for (const [wrong, correct] of Object.entries(monthCorrections)) {
                        corrected = corrected.replace(new RegExp(wrong, 'gi'), correct);
                    }
                    
                    // Semester corrections
                    corrected = corrected.replace(/SEMESTER\s*$$R23$$/i, 'SEMESTER(R23)');
                    corrected = corrected.replace(/SEMESTER\s*$$R22$$/i, 'SEMESTER(R22)');
                    corrected = corrected.replace(/SEMESTER\s*$$R21$$/i, 'SEMESTER(R21)');
                    
                    // Year corrections
                    corrected = corrected.replace(/YEAR\s+II/i, 'II YEAR');
                    corrected = corrected.replace(/YEAR\s+I/i, 'I YEAR');
                    corrected = corrected.replace(/YEAR\s+III/i, 'III YEAR');
                    corrected = corrected.replace(/YEAR\s+IV/i, 'IV YEAR');
                    
                    // Clean up any remaining artifacts
                    corrected = corrected.replace(/[^\w\s\.$$$$\-\/|:]/g, ''); // Keep only alphanumeric, spaces, dots, parentheses, hyphens, slashes, pipes, colons
                    corrected = corrected.replace(/\s+/g, ' ').trim(); // Final space cleanup
                    
                    return corrected;
                }
                
                // Function to validate and fix Hall Ticket Number format
                function validateHallTicketNumber(hallTicket) {
                    if (!hallTicket || typeof hallTicket !== 'string') return hallTicket || '';
                    
                    // Remove any spaces or special characters
                    let cleaned = hallTicket.replace(/[^0-9A-Z]/g, '');
                    
                    // Check if it's exactly 10 characters
                    if (cleaned.length !== 10) return hallTicket;
                    
                    // Check if it matches the pattern: 5 digits + letter + 4 digits
                    const pattern = /^(\d{5})([A-Z])(\d{4})$/;
                    const match = cleaned.match(pattern);
                    
                    if (match) {
                        return cleaned; // Already correct format
                    } else {
                        // Try to fix common OCR errors
                        let fixed = cleaned;
                        // Fix 4->A, O->0, B->8 in the 6th position (letter position)
                        fixed = fixed.replace(/^(\d{5})[4O0B8](\d{4})$/, '$1A$2');
                        return fixed;
                    }
                }

                // Advanced Regex patterns with multiple variations and better accuracy
                const patterns = {
                    // Student Name - Multiple patterns for better detection
                    StudentName: [
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i
                    ],
                    FatherName: [
                        /(FATHER\s*NAME|FATHER\s*S\s*NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(FATHER\s*NAME|FATHER\s*S\s*NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i
                    ],
                    MotherName: [
                        /(MOTHER\s*NAME|MOTHER\s*S\s*NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(MOTHER\s*NAME|MOTHER\s*S\s*NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i
                    ],
                    DOB: [
                        /(DOB|DATE\s*OF\s*BIRTH|BIRTH\s*DATE)\s*[:\-]?\s*([0-9]{1,2}[\/\-\.][0-9]{1,2}[\/\-\.][0-9]{2,4})/i,
                        /(DOB|DATE\s*OF\s*BIRTH|BIRTH\s*DATE)\s*[:\-]?\s*([0-9]{2,4}[\/\-\.][0-9]{1,2}[\/\-\.][0-9]{1,2})/i
                    ],
                    // Enhanced Hall Ticket Number patterns
                    HallTicketNo: [
                        /(HALL\s*TICKET\s*(NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(HALL\s*TICKET\s*(NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(HALL\s*TICKET\s*(NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(HALL\s*TICKET\s*(NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(HALL\s*TICKET\s*(NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i
                    ],
                    RegistrationNo: [
                        /(REG(ISTRATION)?\s*NO|REG\s*NO)\s*[:\-]?\s*([A-Z0-9\/\-]{6,})/i,
                        /(REG(ISTRATION)?\s*NO|REG\s*NO)\s*[:\-]?\s*([A-Z0-9\/\-]{6,})/i
                    ],
                    RollNumber: [
                        /(ROLL\s*(NO|NUMBER)?|ROLL\s*NO)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(ROLL\s*(NO|NUMBER)?|ROLL\s*NO)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i,
                        /(ROLL\s*(NO|NUMBER)?|ROLL\s*NO)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})/i
                    ],
                    // Enhanced University Name patterns
                    UniversityName: [
                        /([A-Z\s]*UNIVERSITY[A-Z\s]*)/i,
                        /([A-Z\s]*UNIVERSITY[A-Z\s]*)/i,
                        /([A-Z\s]*UNIVERSITY[A-Z\s]*)/i,
                        /([A-Z\s]*UNIVERSITY[A-Z\s]*)/i,
                        /([A-Z\s]*UNIVERSITY[A-Z\s]*)/i
                    ],
                    // Enhanced College Name patterns
                    CollegeName: [
                        /(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/i,
                        /(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/i,
                        /(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/i,
                        /(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/i
                    ],
                    Course: [
                        /(B\.?\s*TECH|M\.?\s*TECH|B\.SC|M\.SC|B\.COM|M\.COM|DIPLOMA|MBA|MCA)(?![A-Z\s]*[YEAR\s]*[IVX\d]*[\s]*SEMESTER)/i,
                        /(B\.?\s*TECH|M\.?\s*TECH|B\.SC|M\.SC|B\.COM|M\.COM|DIPLOMA|MBA|MCA)(?![A-Z\s]*[YEAR\s]*[IVX\d]*[\s]*SEMESTER)/i
                    ],
                    Branch: [
                        /(BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+)/i,
                        /(BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+)/i,
                        /(BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+)/i
                    ],
                    ExamMonthYear: [
                        /(MONTH\s*&\s*YEAR\s*OF\s*EXAM|EXAM\s*MONTH\s*&\s*YEAR)\s*[:\-]?\s*([A-Z]+\s*\d{4})/i,
                        /(MONTH\s*&\s*YEAR\s*OF\s*EXAM|EXAM\s*MONTH\s*&\s*YEAR)\s*[:\-]?\s*([A-Z]+\s*\d{4})/i
                    ],
                    CertificateSerialNo: [
                        /(SERIAL\s*NO|SERIAL\s*NUMBER|S\.NO)\s*[:\-]?\s*([A-Z0-9\/\-]+)/i,
                        /(SERIAL\s*NO|SERIAL\s*NUMBER|S\.NO)\s*[:\-]?\s*([A-Z0-9\/\-]+)/i
                    ],
                    SGPA: [
                        /(SGPA|S\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i,
                        /(SGPA|S\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i
                    ],
                    CGPA: [
                        /(CGPA|C\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i,
                        /(CGPA|C\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)/i
                    ],
                    TotalMarks: [
                        /(TOTAL\s*MARKS|TOTAL|GRAND\s*TOTAL)\s*[:\-]?\s*([0-9]{2,4})/i,
                        /(TOTAL\s*MARKS|TOTAL|GRAND\s*TOTAL)\s*[:\-]?\s*([0-9]{2,4})/i
                    ],
                    IssueDate: [
                        /(DATE\s*OF\s*ISSUE|ISSUE\s*DATE|DATE)\s*[:\-]?\s*([0-9]{1,2}[\-\/. ][A-Z]{3,9}[\-\/. ][0-9]{2,4}|[0-9]{2,4}[\-\/. ][0-9]{1,2}[\-\/. ][0-9]{2,4})/i,
                        /(DATE\s*OF\s*ISSUE|ISSUE\s*DATE|DATE)\s*[:\-]?\s*([0-9]{1,2}[\-\/. ][A-Z]{3,9}[\-\/. ][0-9]{2,4}|[0-9]{2,4}[\-\/. ][0-9]{1,2}[\-\/. ][0-9]{2,4})/i
                    ],
                    SerialNo: [
                        /(SERIAL\s*NO|SERIAL\s*NUMBER|S\.NO)\s*[:\-]?\s*([A-Z]{2}[0-9]+)/i,
                        /(SERIAL\s*NO|SERIAL\s*NUMBER|S\.NO)\s*[:\-]?\s*([A-Z]{2}[0-9]+)/i
                    ],
                    ExaminationMonthYear: [
                        /(EXAMINATION)\s*[:\-]?\s*([A-Z\.]+\s+[A-Z]+\s+[A-Z]+\s*$$[A-Z0-9]+$$\s+[A-Z]+)/i,
                        /(EXAMINATION)\s*[:\-]?\s*([A-Z\.]+\s+[A-Z]+\s+[A-Z]+\s*$$[A-Z0-9]+$$\s+[A-Z]+)/i
                    ],
                    MonthYearOfExam: [
                        /(MONTH[\s&]*YEAR\s*OF\s*EXAM)\s*[:\-]?\s*([A-Z\/]+[\s!]*\d{2,4})/i,
                        /(MONTH[\s&]*YEAR\s*OF\s*EXAM)\s*[:\-]?\s*([A-Z\/]+[\s!]*\d{2,4})/i
                    ],
                    AggregateInWords: [
                        /\*\*\*([^*]+)\*\*\*/i,
                        /\*\*\*([^*]+)\*\*\*/i
                    ]
                };

                let data = {};
                for (const [key, patternArray] of Object.entries(patterns)) {
                    // Try each pattern in the array
                    for (const regex of patternArray) {
                        const m = normalized.match(regex);
                        if (m) {
                            let extractedValue = (m[2] || m[3] || m[1]);
                            if (extractedValue && typeof extractedValue === 'string') {
                                extractedValue = extractedValue.trim();
                                // Apply auto-correct to all extracted values
                                data[key] = autoCorrect(extractedValue);
                                break; // Use first successful match
                            }
                        }
                    }
                }
                
                // Advanced heuristics and fallback detection
                if (!data.UniversityName) {
                    // Try multiple approaches for university name
                    const uniLine = lines.find(l => l.toUpperCase().includes('UNIVERSITY'));
                    if (uniLine) {
                        data.UniversityName = autoCorrect(uniLine.toUpperCase());
                    } else {
                        // Look for university in the entire text
                        const uniMatch = upper.match(/([A-Z\s]*UNIVERSITY[A-Z\s]*)/);
                        if (uniMatch) {
                            data.UniversityName = autoCorrect(uniMatch[1]);
                        }
                    }
                }
                
                if (!data.CollegeName) {
                    // Try multiple approaches for college name
                    const colLine = lines.find(l => l.toUpperCase().includes('COLLEGE'));
                    if (colLine) {
                        data.CollegeName = autoCorrect(colLine.toUpperCase());
                    } else {
                        // Look for college patterns
                        const colMatch = upper.match(/(COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)/);
                        if (colMatch) {
                            data.CollegeName = autoCorrect(colMatch[1]);
                        }
                    }
                }
                
                // Enhanced Student Name detection
                if (!data.StudentName) {
                    // Look for name patterns in different formats
                    const namePatterns = [
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i,
                        /(STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})/i
                    ];
                    
                    for (const pattern of namePatterns) {
                        const match = normalized.match(pattern);
                        if (match) {
                            data.StudentName = autoCorrect(match[2] || match[1]);
                            break;
                        }
                    }
                }
                // Enhanced Hall Ticket Number detection with fallback
                if (!data.HallTicketNo) {
                    // Look for 5 digits + letter + 4 digits pattern
                    const htPattern = /\b(\d{5}[A-Z0-9]\d{4})\b/;
                    const htMatch = upper.match(htPattern);
                    if (htMatch) {
                        data.HallTicketNo = validateHallTicketNumber(autoCorrect(htMatch[1]));
                    } else {
                        // Fallback: look for any 10-character alphanumeric pattern
                        const fallbackPattern = /\b([0-9A-Z]{10})\b/;
                        const fallbackMatch = upper.match(fallbackPattern);
                        if (fallbackMatch) {
                            let potentialHT = fallbackMatch[1];
                            data.HallTicketNo = validateHallTicketNumber(potentialHT);
                        }
                    }
                } else {
                    // Validate existing Hall Ticket Number
                    data.HallTicketNo = validateHallTicketNumber(data.HallTicketNo);
                }
                
                // Enhanced Roll Number detection with fallback
                if (!data.RollNumber) {
                    // Look for 5 digits + letter + 4 digits pattern
                    const rollPattern = /\b(\d{5}[A-Z0-9]\d{4})\b/;
                    const rollMatch = upper.match(rollPattern);
                    if (rollMatch) {
                        data.RollNumber = validateHallTicketNumber(autoCorrect(rollMatch[1]));
                    } else {
                        // Fallback: look for any 10-character alphanumeric pattern
                        const fallbackPattern = /\b([0-9A-Z]{10})\b/;
                        const fallbackMatch = upper.match(fallbackPattern);
                        if (fallbackMatch) {
                            let potentialRoll = fallbackMatch[1];
                            data.RollNumber = validateHallTicketNumber(potentialRoll);
                        }
                    }
                } else {
                    // Validate existing Roll Number
                    data.RollNumber = validateHallTicketNumber(data.RollNumber);
                }
                if (!data.ExamMonthYear) {
                    const my = upper.match(/(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)\s+\d{4}/);
                    if (my) data.ExamMonthYear = autoCorrect(my[0]);
                }
                // Fallback for ExaminationMonthYear if not found with label
                if (!data.ExaminationMonthYear) {
                    const my = upper.match(/(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)\s+\d{4}/);
                    if (my) data.ExaminationMonthYear = autoCorrect(my[0]);
                }
                // Fallback for SerialNo - look for pattern anywhere in text
                if (!data.SerialNo) {
                    const sn = normalized.match(/(serimuno|serial\s*no)[\s\-:]*([A-Z]{2}[0-9]+)/i);
                    if (sn) {
                        let serialValue = sn[2] ? sn[2].replace(/\s+/g, '') : sn[1];
                        data.SerialNo = autoCorrect(serialValue);
                    }
                }
                // Additional fallback for just the pattern without label
                if (!data.SerialNo) {
                    // Try multiple patterns for serial numbers, prioritizing 2 letters + numbers
                    const patterns = [
                        /\b[A-Z]{2}[0-9]+\b/,  // Exactly 2 letters followed by numbers
                        /\b[A-Z]{2}[0-9]{4,}\b/,  // Exactly 2 letters followed by 4+ numbers
                        /\b[A-Z]{2}[0-9]{2,}\b/,  // Exactly 2 letters followed by 2+ numbers
                        /\b[A-Z]{1,2}[0-9]{4,}\b/,  // 1-2 letters followed by 4+ numbers
                        /\b[A-Z0-9]{6,}\b/  // Any alphanumeric sequence of 6+ characters
                    ];
                    for (const pattern of patterns) {
                        const sn = upper.match(pattern);
                        if (sn) {
                            const potentialSerial = sn[0].replace(/\s+/g, '');
                            // Validate it looks like a serial number
                            if (potentialSerial.length >= 3 && /[A-Z]/.test(potentialSerial) && /[0-9]/.test(potentialSerial)) {
                                data.SerialNo = autoCorrect(potentialSerial);
                                break;
                            }
                        }
                    }
                }
                // Additional aggressive search for serial numbers - look for any 2-letter + number pattern
                if (!data.SerialNo) {
                    const aggressivePatterns = [
                        /[A-Z]{2}[0-9]{2,}/,  // 2 letters + 2+ numbers (no word boundaries)
                        /[A-Z]{2}[0-9]+/,     // 2 letters + any numbers (no word boundaries)
                    ];
                    for (const pattern of aggressivePatterns) {
                        const matches = upper.match(pattern);
                        if (matches) {
                            const potentialSerial = matches[0].replace(/\s+/g, '');
                            if (potentialSerial.length >= 4) {  // At least 4 characters total
                                data.SerialNo = autoCorrect(potentialSerial);
                                break;
                            }
                        }
                    }
                }
                // Fallback for AggregateInWords - look for text between asterisks
                if (!data.AggregateInWords) {
                    const agg = normalized.match(/\*{3,}([^*]+)\*{3,}/);
                    if (agg) data.AggregateInWords = autoCorrect(agg[1].trim());
                }

                // Data validation and quality improvement
                data = validateAndImproveData(data, normalized);
                
                // Intelligent Pass/Fail Detection
                data.PassStatus = detectPassFailStatus(normalized, data);

                // Render organized sections
                const body = document.getElementById('structuredBody');
                body.innerHTML = '';
                
                // Define sections and their fields
                const sections = {
                    'Student Details': {
                        icon: 'fas fa-user-graduate',
                        fields: ['StudentName', 'FatherName', 'MotherName', 'DOB', 'HallTicketNo', 'RegistrationNo', 'SerialNo']
                    },
                    'University Information': {
                        icon: 'fas fa-university',
                        fields: ['UniversityName', 'CollegeName', 'Branch', 'Course']
                    },
                    'Examination Details': {
                        icon: 'fas fa-calendar-alt',
                        fields: ['ExaminationMonthYear', 'MonthYearOfExam', 'ExamMonthYear', 'IssueDate']
                    },
                    'Academic Performance': {
                        icon: 'fas fa-chart-line',
                        fields: ['TotalMarks', 'SGPA', 'CGPA', 'AggregateInWords']
                    }
                };
                
                // Create sections
                for (const [sectionName, sectionData] of Object.entries(sections)) {
                    const availableFields = sectionData.fields.filter(field => data[field]);
                    if (availableFields.length === 0) continue;
                    
                    const sectionDiv = document.createElement('div');
                    sectionDiv.className = 'section';
                    
                    const sectionTitle = document.createElement('div');
                    sectionTitle.className = 'section-title';
                    sectionTitle.innerHTML = `<i class="${sectionData.icon}"></i>${sectionName}`;
                    sectionDiv.appendChild(sectionTitle);
                    
                    const fieldsGrid = document.createElement('div');
                    fieldsGrid.className = 'fields-grid';
                    
                    for (const fieldKey of availableFields) {
                        const fieldDiv = document.createElement('div');
                        fieldDiv.className = 'field-item';
                        
                        // Determine if this should be full width
                        const fullWidthFields = ['UniversityName', 'CollegeName', 'Branch'];
                        if (fullWidthFields.includes(fieldKey)) {
                            fieldDiv.classList.add('full-width-field');
                        }
                        
                        const fieldName = fieldKey.replace(/([A-Z])/g,' $1').trim().replace(/^./, str => str.toUpperCase());
                        const fieldValue = data[fieldKey] || '';
                        
                        fieldDiv.innerHTML = `
                            <div class="field-label">${escapeHtml(fieldName)}</div>
                            <div class="field-value">${escapeHtml(fieldValue)}</div>
                        `;
                        
                        fieldsGrid.appendChild(fieldDiv);
                    }
                    
                    sectionDiv.appendChild(fieldsGrid);
                    body.appendChild(sectionDiv);
                }
                
                // Add Achievement section if available
                if (data.AggregateInWords && typeof data.AggregateInWords === 'string') {
                    const achievementDiv = document.createElement('div');
                    achievementDiv.className = 'achievement-section';
                    achievementDiv.innerHTML = `
                        <div class="achievement-title">Achievement</div>
                        <div class="achievement-value">${escapeHtml(data.AggregateInWords)}</div>
                    `;
                    body.appendChild(achievementDiv);
                }
                
                // Add Result section if available
                if (data.PassStatus) {
                    const resultDiv = document.createElement('div');
                    resultDiv.className = 'result-section';
                    
                    let resultText, resultClass;
                    
                    // Handle both old format (string) and new format (object)
                    if (typeof data.PassStatus === 'object' && data.PassStatus.status) {
                        resultText = data.PassStatus.status;
                        
                        // Add reasoning as tooltip or additional info
                        if (data.PassStatus.reasoning) {
                            resultDiv.title = `Reasoning: ${data.PassStatus.reasoning}`;
                        }
                    } else {
                        // Fallback for old string format
                        resultText = data.PassStatus;
                    }
                    
                    resultClass = 'result-value';
                    
                    // Style based on pass/fail status
                    if (resultText.toUpperCase() === 'PASSED') {
                        resultText = 'PASSED';
                    } else if (resultText.toUpperCase() === 'FAILED') {
                        resultText = 'FAILED';
                    }
                    
                    resultDiv.innerHTML = `
                        <div class="result-title">Result</div>
                        <div class="${resultClass}">${escapeHtml(resultText)}</div>
                    `;
                    body.appendChild(resultDiv);
                }
                
                if (body.children.length === 0) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'section text-center';
                    emptyDiv.innerHTML = `
                        <div class="section-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            No Data Found
                        </div>
                        <div class="field-value">No structured fields detected. Please try with a different document.</div>
                    `;
                    body.appendChild(emptyDiv);
                }
            }


            function escapeHtml(str) {
                if (typeof str !== 'string') {
                    return String(str || '');
                }
                return str.replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
            }

            // Save OCR data to database
// Update the saveOcrDataToDatabase function to return a Promise
function saveOcrDataToDatabase(ocrData, fileName) {
    return new Promise((resolve, reject) => {
        if (!window.currentExtractedFields) {
            reject(new Error('No extracted fields available'));
            return;
        }

        const extractedData = window.currentExtractedFields;
        const confidence = ocrData.confidence || 0.6;

        // Prepare data for saving
        const saveData = {
            extracted_fields: extractedData,
            confidence: confidence,
            file_name: fileName
        };

        console.log('Saving data to database:', saveData);

        fetch('api.php?action=save_ocr_data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(saveData)
        })
        .then(response => {
            // First, check the content type
            const contentType = response.headers.get('content-type');
            console.log('Response content type:', contentType);
            
            // Get the raw text to see what's actually returned
            return response.text().then(text => {
                console.log('Raw response:', text);
                
                // Try to parse as JSON if it looks like JSON
                if (contentType && contentType.includes('application/json')) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + text.substring(0, 200));
                    }
                } else {
                    // If it's not JSON, throw an error with the raw text
                    throw new Error('Non-JSON response: ' + text.substring(0, 200));
                }
            });
        })
        .then(saveResult => {
            if (saveResult.error) {
                console.error('Failed to save OCR data:', saveResult.error);
                reject(new Error(saveResult.detail || saveResult.error));
            } else {
                console.log('OCR data saved successfully with ID:', saveResult.id);
                resolve(saveResult);
            }
        })
        .catch(error => {
            console.error('Error saving OCR data:', error);
            reject(error);
        });
    });
}            // Process new image
            newImageBtn.addEventListener('click', () => {
                selectedFile = null;
                fileInput.value = '';
                filePreview.classList.add('hidden');
                resultSection.classList.add('hidden');
            });
        });
    </script>
</body>
</html>
        