// Function to open file uploader and handle file uploads
function openFileUploader() {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = ".pdf,.jpg,.jpeg,.png";  // Accept supported formats

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            uploadFile(file);
        }
    });

    fileInput.click();  // Trigger file upload dialog
}

// Function to upload the file to the backend and handle preview update
function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);

    fetch('process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())  // Expect JSON response from server
    .then(data => {
        if (data.success) {
            // Update the preview section dynamically
            addFileToPreview(data.file_name, data.file_path, data.pages);
            // Update cart summary based on new file upload
            updateCartSummaryWithNewFile(data.pages, data.price);
        } else {
            alert('File upload failed: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to add the uploaded file to the preview section
function addFileToPreview(fileName, filePath, pages) {
    const previewBox = document.querySelector('.file-preview');
    
    const fileCard = document.createElement('div');
    fileCard.classList.add('file-card');

    const img = document.createElement('img');
    img.src = filePath;  // Use the uploaded file's path for preview
    img.alt = 'File Preview';

    const fileDescription = document.createElement('p');
    fileDescription.textContent = `${fileName} (${pages} pages)`;

    fileCard.appendChild(img);
    fileCard.appendChild(fileDescription);
    
    previewBox.insertBefore(fileCard, previewBox.querySelector('.add-file-card'));  // Insert before the "Add files" box
}

// Function to update cart summary based on the new file upload
function updateCartSummaryWithNewFile(pages, pricePerPage) {
    const totalPagesElement = document.getElementById('total-pages');
    const totalAmountElement = document.getElementById('total-amount');
    
    // Parse current totals
    let currentPages = parseInt(totalPagesElement.innerText.match(/\d+/)[0], 10);
    let currentAmount = parseInt(totalAmountElement.innerText.replace('₹', ''), 10);
    
    // Update totals
    currentPages += pages;
    currentAmount += pages * pricePerPage;

    totalPagesElement.innerText = `Total ${currentPages} page${currentPages > 1 ? 's' : ''}`;
    totalAmountElement.innerText = `₹${currentAmount}`;
    
    // Also update the receipt sidebar
    const receiptSidebar = document.getElementById('receipt-sidebar');
    
    const receiptItem = document.createElement('div');
    receiptItem.classList.add('receipt-item');
    receiptItem.innerText = `${pages} page${pages > 1 ? 's' : ''}: ₹${pages * pricePerPage}`;
    
    receiptSidebar.appendChild(receiptItem);
}

// Function to handle sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('receipt-sidebar');
    sidebar.classList.toggle('active');  // Assuming you have CSS for toggling
}

// Attach event listener to sidebar toggle button
document.getElementById('toggle-sidebar-btn').addEventListener('click', toggleSidebar);
